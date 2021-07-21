<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\CustomerController;
use App\Http\Controllers\api\v1\UserController;
use App\Http\Controllers\api\v1\AuthController;
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

Route::group(['prefix' => 'v1'], function(){
    //user authentication
    Route::group(['prefix' => 'auth'], function(){
       Route::post('/register',[AuthController::class,'register']);
       Route::post('/login',[AuthController::class,'login']);
       Route::group(['middleware' => 'auth:sanctum'], function(){
            Route::post('/logout',[AuthController::class,'logout']);
       });
    });
    //customer routes
    Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'customers'], function(){
        //Route::resource('customers',CustomerController::class);
        Route::get('/',[CustomerController::class,'index']);
        Route::get('/{id}/details',[CustomerController::class,'show']);
        Route::post('/create',[CustomerController::class,'store']);
        Route::patch('/{id}/update',[CustomerController::class,'update']);
        Route::delete('/{id}/delete',[CustomerController::class,'destroy']);
    });
});
