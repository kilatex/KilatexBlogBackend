<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth Routes
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
 //   Route::get('/isauth', [AuthController::class, 'isAuth']);
    Route::post('/register', [AuthController::class, 'register']);
});

// User Routes
Route::group([
    'middleware' => 'api',
    'prefix' => 'user'
], function ($router) {
    Route::post('/update', [UserController::class,'update']);
    Route::get('/all', [UserController::class,'getAll']);
    Route::get('/latest', [UserController::class,'latestUsers']);
    Route::post('/upload-avatar', [UserController::class,'uploadAvatar']);
    Route::get('/avatar/{filename}', [UserController::class,'getAvatar']);
    Route::get('/get/{id}', [UserController::class,'getUser']);
    Route::get('/search/{username}', [UserController::class,'search']);
});

// Category ROUTES
Route::group([
    'middleware' => 'api',
], function ($router) {
   Route::resource('/category', '\App\Http\Controllers\CategoryController');
});

// Post Routes
Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::resource('/post', '\App\Http\Controllers\PostController');
    Route::get('/post/image/{filename}', [PostController::class,'getImage']);
    Route::get('/post/user/{id}', [PostController::class,'postsByUser']);
    Route::get('/post/category/{id}', [PostController::class,'postsByCategory']);
});