<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:150'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:150', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'phone_number' => 'sometimes|string|max:15',
                'avatar_url' => 'sometimes|string|max:150',
                'status' => 'sometimes|boolean',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'avatar_url' => $request->avatar_url,
                'status' => $request->status,
            ]);
            $role = Role::findByName('customer');
            $user->assignRole($role)->firstOrFail();
            $user->save();
            event(new Registered($user));

            Auth::login($user);

            $token = $user->createToken('api-token');
            return response()->json([
                'success' => true,
                'message' => 'success',
                'user' => new UserResource($user),
                'token' => $token->plainTextToken,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the user.',
                'error' => $e->getMessage() // Trả về thông điệp lỗi chi tiết
            ], 500);
        }
    }
}
