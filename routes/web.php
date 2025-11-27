<?php

declare(strict_types=1);

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/{path?}', function () {
    $path = request()->path();

    // 🔹 API routes
    if (str_starts_with($path, 'api/')) {
        return app()->handle(request());
    }

    // 🔹 For root path "/" - serve index.html directly
    if ($path === '' || $path === '/') {
        $indexPath = public_path('build/index.html');
        if (File::exists($indexPath)) {
            return response()->file($indexPath);
        }
    }

    // 🔹 Serve static assets (js, css, images)
    $filePath = public_path("build/{$path}");
    if (File::exists($filePath)) {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimeTypes = [
            'js' => 'application/javascript',
            'mjs' => 'application/javascript',
            'css' => 'text/css',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
        ];

        $mimeType = $mimeTypes[$extension] ?? 'text/html';

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000, immutable'
        ]);
    }

    // 🔹 SPA Fallback - serve index.html for React Router
    $indexPath = public_path('build/index.html');
    if (File::exists($indexPath)) {
        return response()->file($indexPath);
    }

    // 🔹 Debug info if index.html missing
    return response()->json([
        'error' => 'Build folder not found',
        'path' => public_path('build'),
        'exists' => File::exists(public_path('build')),
        'index_exists' => File::exists(public_path('build/index.html')),
        'build_contents' => File::exists(public_path('build')) ? scandir(public_path('build')) : []
    ], 500);
})->where('path', '.*');

