<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        try {
            if ($request->user()->hasVerifiedEmail()) {
                return redirect()->intended(RouteServiceProvider::HOME);
            }

            $request->user()->sendEmailVerificationNotification();

            return response()->json(['status' => 'verification-link-sent']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() // Trả về thông điệp lỗi chi tiết
            ], 500);
        }
    }
}
