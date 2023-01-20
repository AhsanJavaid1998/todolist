<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\AuthController;

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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::post('email-verification', [AuthController::class, 'emailVerification']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::group(['middleware' => ['is_email_verify']], function() {
        Route::resource('todo', TodoController::class);
        Route::post('todo/search', [TodoController::class, 'search']);
    });
});
