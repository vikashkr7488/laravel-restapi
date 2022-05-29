<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SeekerController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::get('job/seekers', [SeekerController::class, 'index']);
Route::get('job/seeker/{id}', [SeekerController::class, 'seekerShow']);


Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('auth/user', [AuthController::class, 'user']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('user/profile', [SeekerController::class, 'create']);
    Route::put('user/{id}/update', [SeekerController::class, 'update']);
    Route::delete('user/{id}/delete', [SeekerController::class, 'delete']);

    Route::apiResource('category', CategoryController::class);
});

