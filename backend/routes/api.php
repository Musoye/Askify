<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\RoleMiddleware;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');



// Route::middleware([RoleMiddleware::class . ':admin'])->group(function () {
//     Route::get('/admin-dashboard', function () {
//         // Only accessible by users with role 'admin'
//     });

//     Route::get('/admin-settings', function () {
//         // Only accessible by users with role 'admin'
//     })->withoutMiddleware([RoleMiddleware::class . ':admin']);
// });



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
