<?php

namespace App\Http\Controllers;

use App\Filters\PostFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Lấy người dùng hiện tại
        $user = Auth::user();

        // Lấy danh sách các bài viết mà người dùng có quyền xem
        $posts = Post::where('deleted', 0)->latest()->get()->filter(function ($post) use ($user) {
            return $user->can('view', $post);
        });
        if ($request->hasAny(['filter'])) {
            $filters = new PostFilter($request);
            $posts = Post::filter($filters)->get();
        }
        return response()->json(PostResource::collection($posts));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $staff_id = $request->user()->id;
            $validatedData = $request->validate([
                'title' => 'required|string|max:150',
                'content' => 'required|string',
                'image_url' => 'sometimes|nullable|string|max:150'
            ]);
            $post = Post::create([
                'staff_id' => $staff_id,
                'title' => data_get($validatedData, 'title'),
                'content' => data_get($validatedData, 'content'),
                'image_url' => data_get($validatedData, 'image_url'),
            ]);

            return new PostResource($post);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
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
                'title' => 'sometimes|string|max:150',
                'content' => 'sometimes|string',
                'image_url' => 'sometimes|nullable|string|max:150'
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
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'No ids provided.'], Response::HTTP_BAD_REQUEST);
        }
        try {
            $posts = Post::whereIn('id', $ids)->get();
            // Kiểm tra quyền 
            foreach ($posts as $post) {
                $this->authorize('delete', $post);
                // Kiểm tra bài viết tồn tại và chưa bị xóa trước đó
                if (!$post || $post->deleted) {
                    return response()->json(['message' => 'One or more posts do not exist or have already been deleted.'], Response::HTTP_NOT_FOUND);
                }
            }
            Post::whereIn('id', $ids)->update(['deleted' => 1]);

            return response()->json(['message' => 'Posts deleted successfully.'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
