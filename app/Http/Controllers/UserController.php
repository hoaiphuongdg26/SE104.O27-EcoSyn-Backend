<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy người dùng hiện tại
        $current_user = Auth::user();

        $users = User::where('deleted', 0)->latest()->get()->filter(function ($user) use ($current_user) {
            return $current_user->can('view', $user);
        });
        return response()->json(UserResource::collection($users));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('view', $user)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }
            return new UserResource($user);
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
            $user = User::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('update', $user)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }
            $validatedData = $request->validate([
                'name'     => 'sometimes|nullable|string|max:150',
                'email'   => 'sometimes|nullable|string|lowercase|email|max:150|unique:'.User::class,
                'password' => 'sometimes|nullable|string|max:150',
                'role'     => 'sometimes|exists:roles,name'
            ]);

            $user->syncRoles([$request->input('role')]);

            $user->update($validatedData);

            return new UserResource($user);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::where('deleted', 0)->findOrFail($id);
            if (!auth()->user()->can('delete', $user)) {
                return response()->json(['message' => 'You do not have permission this action.'], Response::HTTP_FORBIDDEN);
            }
            $user->deleted = 1;
            $user->save();
            return response()->json(['message' => 'User deleted'], Response::HTTP_ACCEPTED);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
