<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\CustomerController;
use App\Http\Controllers\api\v1\UserController;
use App\Http\Controllers\api\v1\AuthController;
use App\Http\Controllers\api\v1\ProfileController;
use App\Http\Controllers\api\v1\RoleController;
use App\Http\Controllers\api\v1\PermissionController;
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

    Route::group(['middleware' => 'auth:sanctum'], function(){
        //customer management (both a user and an admin can manage customers )
        Route::group(['prefix' => 'customers','middleware' => ['role:user|admin']], function(){
            //Route::resource('customers',CustomerController::class);
            Route::get('/',[CustomerController::class,'index']);
            Route::get('/{id}/details',[CustomerController::class,'show']);
            Route::post('/create',[CustomerController::class,'store']);
            Route::patch('/{id}/update',[CustomerController::class,'update']);
            Route::delete('/{id}/delete',[CustomerController::class,'destroy']);
        });
        //user management (Only admin can perform user management actions)
        Route::group(['prefix' => 'users','middleware' => ['role:admin']], function(){
            Route::get('/',[UserController::class,'index']);
            Route::get('/{id}/details',[UserController::class,'show']);
            Route::post('/create',[UserController::class,'store']);
            Route::patch('/{id}/update',[UserController::class,'update']);
            Route::delete('/{id}/delete',[UserController::class,'destroy']);
            //Admin status(make a user an admin if not already
            Route::group(['prefix' => 'status'], function(){
                Route::post('/{id}/admin',[UserController::class,'makeAdmin']);
            });
        });
        //user profile management
        Route::group(['prefix' => 'user/profile', 'middleware' => ['role:user|admin']], function(){
            Route::get('/',[ProfileController::class,'profile']);
            Route::patch('/update',[ProfileController::class,'update']);
        });
        //Roles and Permissions(Only admin can manage roles and permissions)
        Route::group(['middleware' => ['role:admin']], function(){
            Route::resource('roles',RoleController::class);
            Route::resource('permissions', PermissionController::class);
        });
    });
});
