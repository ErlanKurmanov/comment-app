<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\VideoPostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{news}', [NewsController::class, 'show']);

Route::get('/videos', [VideoPostController::class, 'index']);
Route::get('/videos/{videoPost}', [VideoPostController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/news', [NewsController::class, 'store']);
    Route::post('/videos', [VideoPostController::class, 'store']);

    Route::post('/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});
