<?php

use Illuminate\Http\Request;

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

/*
|--------------------------------------------------------------------------
| API Fallback
|--------------------------------------------------------------------------
|
| Route for Not Found Errors
|
|
*/


Route::fallback(function(){
    return response()->json([
        "message" => "404 Resource Not Found",
        
    ],404);
});

/*
|--------------------------------------------------------------------------
| API Fallback End
|--------------------------------------------------------------------------
|
*/






Route::apiResource('departments','DepartmentController');

Route::apiResource('admins','AdminController',['except'=>[
    'store'
]]);

Route::apiResource('registrars','RegistrarController');




Route::get("test","AdminController@checkRoleUser");

Route::group(["prefix"=>"users"],function(){
    Route::post('login','UserController@login');
    Route::get('images/{id}','UserController@image')->name('users.image') ;
});

