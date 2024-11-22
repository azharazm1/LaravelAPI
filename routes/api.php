<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/** Menambahkan prefix v1 pada route api (http://localhost:8000/api/v1/route_yang_diakses) */
Route::prefix('v1')->group(function(){
    Route::get('list-articles',[App\Http\Controllers\API\v1\ArticleController::class,'index']);
    Route::post('store-articles',[App\Http\Controllers\API\v1\ArticleController::class,'store'])->middleware('api');
    Route::get('read-articles/{id}',[App\Http\Controllers\API\v1\ArticleController::class,'show']);
    Route::put('update-articles/{id}',[App\Http\Controllers\API\v1\ArticleController::class,'update']);
});

// Route::get('list-articles',[App\Http\Controllers\API\v1\ArticleController::class,'index']);