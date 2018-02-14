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


Route::fallback(function () {
    return response()->json([
        "externalMessage" => "404 Resource Not Found",
        "internalMessage" => "Cannot Find Route"

    ], 404);
});

/*
|--------------------------------------------------------------------------
| API Fallback End
|--------------------------------------------------------------------------
|
 */



//users and profiles



Route::apiResource('students', 'StudentController');

Route::apiResource('admins', 'AdminController', ['except' => [
    'store'
]]);

Route::apiResource('registrars', 'RegistrarController');
Route::group(['prefix' => 'registrars'], function () {
    Route::get('/{id}/students', 'RegistrarController@registeredStudents')->name('registrars.students');

    Route::group(['prefix' => 'trashed'], function () {

        Route::get('index', 'RegistrarController@trashedIndex')->name('registrars.trashed');

        Route::get('/{registrar}', 'RegistrarController@showTrashed')->name('registrars.trashed.show');

        Route::put('/{registrar}', 'RegistrarController@restore')->name('registrars.restore');

    });
});

Route::apiResource('comelecs', 'ComelecController');
Route::group(['prefix' => 'comelecs'], function () {

    Route::group(['prefix' => 'trashed'], function () {

        Route::get('index', 'ComelecController@trashedIndex')->name('comelecs.trashed');

        Route::get('/{comelec}', 'ComelecController@showTrashed')->name('comelecs.trashed.show');

        Route::put('/{comelec}', 'ComelecController@restore')->name('comelecs.restore');

    });
});


Route::group(["prefix" => "users"], function () {
    Route::post('login', 'UserController@login');
    Route::get('images/{id}', 'UserController@image')->name('users.image');
});




//school settings

Route::apiResource('departments', 'DepartmentController', ['only' => [
    'index', 'show'
]]);


Route::apiResource('school_years', 'SchoolYearController');
Route::group(['prefix' => 'school_years'], function () {

    Route::group(['prefix' => 'active'], function () {

        Route::get('index', 'SchoolYearController@getActiveSchoolYear')->name('school_years.active.show');
        Route::put('/{school_year}', 'SchoolYearController@activateSchoolYear')->name('school_years.active.activate');


    });

    Route::group(['prefix' => 'trashed'], function () {

        Route::get('index', 'SchoolYearController@trashedIndex')->name('school_years.trashed');

        Route::get('/{school_year}', 'SchoolYearController@showTrashed')->name('school_years.trashed.show');

        Route::put('/{school_year}', 'SchoolYearController@restore')->name('school_years.restore');

    });


});


Route::apiResource('year_levels', 'YearLevelController');
Route::group(['prefix' => 'year_levels'], function () {



    Route::group(['prefix' => 'trashed'], function () {

        Route::get('index', 'YearLevelController@trashedIndex')->name('year_levels.trashed');

        Route::get('/{year_level}', 'YearLevelController@showTrashed')->name('year_levels.trashed.show');

        Route::put('/{year_level}', 'YearLevelController@restore')->name('year_levels.restore');

    });


});


Route::apiResource('colleges', 'CollegeController');
Route::group(['prefix' => 'colleges'], function () {

    Route::group(['prefix' => 'trashed'], function () {

        Route::get('index', 'CollegeController@trashedIndex')->name('colleges.trashed');

        Route::get('/{college}', 'CollegeController@showTrashed')->name('colleges.trashed.show');

        Route::put('/{college}', 'CollegeController@restore')->name('colleges.restore');

    });

});


// election
Route::apiResource('elections', 'ElectionController');

Route::group(['prefix' => 'elections'], function () {
    Route::group(['prefix' => '{election}'], function () {
        Route::apiResource('positions', 'PositionController');
        Route::apiResource('candidates', 'CandidateController');
        Route::apiResource('partylists', 'PartylistController');
        Route::apiResource('votes', 'VoteController');
        Route::get('standings', 'CandidateController@standing');
    });

    Route::get('opened', 'ElectionController@opened');
    Route::get('published', 'ElectionController@published');
});