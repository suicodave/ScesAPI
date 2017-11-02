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



Route::apiResource('departments','DepartmentController');


Route::post('admin/login','AdminController@login');

Route::get('/users',function(App\User $user){
    return response()->json($user->all());
})->middleware('jwtAuth');


Route::fallback(function(){
    return response()->json([
        "message" => "404 Resource Not Found",
        
    ],404);
});