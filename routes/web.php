<?php

use Core\Route;
use App\Controllers\PagesController;
use App\Controllers\PostsController;
use App\Controllers\UsersController;
use App\Controllers\DashboardController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

// Homepage
Route::get('/', [PagesController::class, 'home']);


// Posts - Public Routes
Route::get('/posts', [PostsController::class, 'index']);
Route::get('/posts/{id}', [PostsController::class, 'show']);

// Posts - Protected routes (require authentication)
Route::get('/posts/create', [PostsController::class, 'create'])->middleware(AuthMiddleware::class);
Route::post('/posts', [PostsController::class, 'store'])->middleware(AuthMiddleware::class);
Route::get('/posts/{id}/edit', [PostsController::class, 'edit'])->middleware(AuthMiddleware::class);
Route::post('/posts/{id}', [PostsController::class, 'update'])->middleware(AuthMiddleware::class); // Momentarily we'll use POST to emulate PUT and PATCH
Route::post('/posts/{id}/delete', [PostsController::class, 'destroy'])->middleware(AuthMiddleware::class);


// Users - Guest only (redirect to dashboard if authenticated)
Route::get('/register', [UsersController::class, 'register'])->middleware(GuestMiddleware::class);
Route::post('/register', [UsersController::class, 'store'])->middleware(GuestMiddleware::class);
// Route::get('/login', [UsersController::class, 'login'])->middleware(GuestMiddleware::class);
// Route::post('/login', [UsersController::class, 'authenticate'])->middleware(GuestMiddleware::class);

Route::group(['middleware' => [GuestMiddleware::class]], function () {
    Route::get('/login', [UsersController::class, 'login'])->middleware(GuestMiddleware::class);
    Route::post('/login', [UsersController::class, 'authenticate'])->middleware(GuestMiddleware::class);
});

// Logout (requres auth)
Route::get('/logout', [UsersController::class, 'logout'])->middleware(AuthMiddleware::class);

// Pages
Route::get('/home', [PagesController::class, 'home']);
Route::get('/about', [PagesController::class, 'about']);

// Dashboard - requires authentication
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(AuthMiddleware::class);

// Or use groups for cleaner syntax:
// Route::group(['middleware' => [AuthMiddleware::class], 'prefix' => '/admin'], function () {
//     // All routes here require authentication and are prefixed with /admin
//     Route::get('/users', [AdminController::class, 'users']); // /admin/users
// });
