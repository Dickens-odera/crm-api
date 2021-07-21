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
    });
});
