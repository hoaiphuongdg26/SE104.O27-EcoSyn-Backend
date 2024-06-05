<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MigrationController extends Controller
{
    public function runMigration(Request $request)
    {
        try {
            Artisan::call('migrate');
            return response()->json(['message' => 'Migration ran successfully']);
        } catch (\Exception $e) {
            // Log lỗi để kiểm tra chi tiết
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}


