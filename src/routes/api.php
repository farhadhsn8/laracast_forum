<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Version 1 of the forum REST API.
|
*/

Route::prefix('v1')->group(function () {
    // Authentication Routes
    include __DIR__.'/v1/auth_routes.php';

    // User Routes
    include __DIR__.'/v1/user_routes.php';

    // Channel Routes
    include __DIR__.'/v1/channels_routes.php';

    // Thread Routes
    include __DIR__.'/v1/threads_routes.php';
});
