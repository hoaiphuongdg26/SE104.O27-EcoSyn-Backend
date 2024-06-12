<?php

namespace App\Http\Controllers;

use App\Filters\UserFilter;
use App\Http\Resources\UserResource;
use App\Http\Resources\VehicleResource;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Lấy người dùng hiện tại
        $current_user = Auth::user();

        $users = User::where('deleted', 0)->latest()->get()->filter(function ($user) use ($current_user) {
            return $current_user->can('view', $user);
        });
        if ($request->hasAny(['filter'])) {
            $filters = new UserFilter($request);
            $users = User::filter($filters)->get();
        }
        return response()->json(UserResource::collection($users));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:150', 'unique:' . User::class],
            'phone_number' => 'sometimes|string|max:15',
            'avatar_url' => 'sometimes|string|max:150',
            'status' => 'sometimes|boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('@12345678'),
            'phone_number' => $request->phone_number,
            'avatar_url' => $request->avatar_url,
            'status' => $request->status,
        ]);

        return new UserResource($user);
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
                'name' => 'sometimes|nullable|string|max:150',
                'email' => 'sometimes|nullable|string|lowercase|email|max:150|unique:' . User::class,
                'password' => ['sometimes', 'confirmed', Rules\Password::defaults()],
                'role' => 'sometimes|exists:roles,name',
                'phone_number' => 'sometimes|string|max:15',
                'avatar_url' => 'sometimes|string|max:150',
                'status' => 'sometimes|boolean',
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
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'No ids provided.'], Response::HTTP_BAD_REQUEST);
        }
        try {
            $users = User::whereIn('id', $ids)->get();
            // Kiểm tra quyền xóa cho từng người dùng
            foreach ($users as $user) {
                $this->authorize('delete', $user);
            }
            // Cập nhật trạng thái xóa của các người dùng đã chọn
            User::whereIn('id', $ids)->update(['deleted' => 1]);

            return response()->json(['message' => 'Users deleted successfully.'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getVehiclesByUser($userId)
    {
        // Lấy user theo id
        $user = User::findOrFail($userId);
        // Lấy tất cả các vehicle thuộc về user này
        $vehicles = $user->vehicles()->where('vehicles.deleted', 0)->get();
        return response()->json(VehicleResource::collection($vehicles));
    }
}
