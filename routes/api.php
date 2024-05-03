<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\api\SenderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/categories', [CategoriesController::class, 'index']);
Route::get('/categories/{id}', [CategoriesController::class, 'show']);
Route::post('/categories/store', [CategoriesController::class, 'store']);
Route::post('/categories/update/{id}', [CategoriesController::class, 'update']);
Route::delete('/categories/{id}', [CategoriesController::class, 'destroy']);

Route::get('/products', [CategoriesController::class, 'index']);
Route::get('/products/{id}', [CategoriesController::class, 'show']);
Route::post('/products/store', [CategoriesController::class, 'store']);
Route::post('/products/update/{id}', [CategoriesController::class, 'update']);
Route::delete('/products/{id}', [CategoriesController::class, 'destroy']);

Route::post('/send/email', [SenderController::class, 'send_email']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/login/google', [AuthController::class, 'loginGoogle']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verification', [AuthController::class, 'verificationEmail']);
