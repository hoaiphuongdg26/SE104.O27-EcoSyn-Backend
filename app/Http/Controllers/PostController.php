<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\V1\PostResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy người dùng hiện tại
        $user = Auth::user();

        // Lấy danh sách các bài viết mà người dùng có quyền xem
        $posts = Post::where('deleted', 0)->latest()->get()->filter(function ($post) use ($user) {
            return $user->can('view', $post);
        });
        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $staff_id = $request->user()->id;
        \Illuminate\Support\Facades\Log::info('User ID: ' . $staff_id);
        $validatedData = $request->validate([
            'title'     => 'sometimes|string|max:150',
            'content'   => 'sometimes|string',
            'image_url' => 'sometimes|string|max:150'
        ]);

        $post = Post::create([
            'staff_id'  => $staff_id,
            'title'     => $validatedData['title'],
            'content'   => $validatedData['content'],
            'image_url' => $validatedData['image_url'] ?? null,
            'deleted'   => 0
        ]);

        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $post = Post::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('view', $post)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }
            return new PostResource($post);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $post = Post::where('deleted', 0)->findOrFail($id);

            if (!auth()->user()->can('update', $post)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }

            $validatedData = $request->validate([
                'title'     => 'sometimes|string|max:150',
                'content'   => 'sometimes|string',
                'image_url' => 'sometimes|string|max:150'
            ]);

            $post->update($validatedData);

            return new PostResource($post);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Post not found'], Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $post = Post::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('delete', $post)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }
            $post->deleted = 1;
            $post->save();
            return response()->json(['message' => 'Post deleted'], Response::HTTP_ACCEPTED);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
