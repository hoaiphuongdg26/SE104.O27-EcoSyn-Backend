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
            Log::info('Starting migration...');
            Artisan::call('migrate', ['--force' => true]);
            Log::info('Migration output: ' . Artisan::output());
            return response()->json(['message' => 'Migration ran successfully']);
        } catch (\Exception $e) {
            Log::error('Migration error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}


