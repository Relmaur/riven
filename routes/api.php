<?php

declare(strict_types=1);

use Core\Route;
use App\Controllers\Api\PostApiController;

Route::get('/api/posts', [PostApiController::class, 'index']);
Route::get('/api/posts/{id}', [PostApiController::class, 'show']);
