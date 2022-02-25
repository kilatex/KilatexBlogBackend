<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;


// Classes
use App\Http\Middleware\ApiAuthMiddleware;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});




// USER ROUTES
Route::post('api/register', [UserController::class,'register']);
Route::post('api/login', [UserController::class,'login']);
Route::put('api/update', [UserController::class,'update']);
Route::post('api/upload-avatar', [UserController::class,'uploadAvatar'])->middleware(ApiAuthMiddleware::class);
Route::get('api/get-image/{filename}', [UserController::class,'getAvatar']);
Route::get('api/get-user/{id}', [UserController::class,'getUser']);


// CATEGORY ROUTES
Route::resource('api/category', '\App\Http\Controllers\CategoryController');