<?php

// Load the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Check if the request is for the API
$isApiRequest = stripos($_SERVER['REQUEST_URI'], '/api/') === 0;

// Set the correct path for API requests
if ($isApiRequest) {
    $app->instance('path', base_path('routes/api.php'));
}

// Run the application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);
