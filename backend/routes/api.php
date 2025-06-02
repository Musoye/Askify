<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Middleware\AdminMiddleware;

// User DEFINITION

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// DOCUMENT DEFINITION

Route::post('/documents', [DocumentController::class, 'store'])->middleware(['auth:sanctum', AdminMiddleware::class]);
Route::get('/documents', [DocumentController::class, 'index'])->middleware('auth:sanctum');
Route::get('/documents/{document}', [DocumentController::class, 'show'])->middleware('auth:sanctum');
Route::put('/documents/{document}', [DocumentController::class, 'update'])->middleware(['auth:sanctum', AdminMiddleware::class]);
Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->middleware(['auth:sanctum', AdminMiddleware::class]);
Route::get('/documents/{document}/view', [DocumentController::class, 'view']);




// Route::middleware([AdminMiddleware::class . ':admin'])->group(function () {
//     Route::get('/admin-dashboard', function () {
//         // Only accessible by users with role 'admin'
//     });

//     Route::get('/admin-settings', function () {
//         // Only accessible by users with role 'admin'
//     })->withoutMiddleware([AdminMiddleware::class . ':admin']);
// });



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
