<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VoucherController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix("auth")->group(function(){
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);


        Route::middleware(JwtMiddleware::class)->group(function(){

            Route::delete('logout', [AuthController::class, 'logout']);
            Route::get('user/current', [AuthController::class, 'getcurrrentuser']);
            Route::patch('refresh',[AuthController::class, 'refresh']);
        });
});


Route::prefix("products")->group(function(){
    Route::middleware(JwtMiddleware::class)->group(function(){

        Route::controller(ProductController::class)->group(function(){
            Route::get('' ,'get');
            Route::get('detail/{id}' ,'getdetail');
            Route::get('search' ,'search');
            Route::put('{id}' ,'update');
            Route::post('' ,'create');
            Route::delete('{id}' ,'delete');
        });
    });
});

Route::prefix("vouchers")->group(function(){
    Route::middleware(JwtMiddleware::class)->group(function(){

        Route::controller(VoucherController::class)->group(function(){
            Route::get('' ,'get');
            Route::get('detail/{id}' ,'getdetail');
            Route::put('{id}' ,'update');
            Route::post('' ,'create');
            Route::delete('{id}' ,'delete');
            Route::patch('apply' ,'apply');
        });
    });
});

