<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validate dữ liệu
            $creditials = [
                'email' => $request->email,
                'password' => $request->password
            ];
            // Kiểm tra thông tin đăng nhập
            if (!Auth::attempt($creditials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email or Password is incorrect',
                    'user' => null
                ], 401);
            }
            // Nếu thông tin đăng nhập chính xác, tạo token
            $token = $request->user()->createToken('authToken')->accessToken;
            return response()->json([
                'success' => true,
                'message' => 'success',
                'user' => Auth::user(),
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the user.',
                'error' => $e->getMessage() // Trả về thông điệp lỗi chi tiết
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            // Kiểm tra xem email đã tồn tại trong cơ sở dữ liệu chưa
            if (User::where('email', $request->email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already exists',
                    'user' => null
                ], 400);
            }
            // Validate dữ liệu
            $data = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            $data->id = $data->getKey('uuid');
            return response()->json([
                'success' => true,
                'message' => 'success',
                'user' => $data
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
