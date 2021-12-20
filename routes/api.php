<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['namespace' => 'App\Http\Controllers\Auth','prefix' => '/auth'], function () {
    Route::post('login', 'AuthController@authenticate');
    Route::get('logout','AuthController@logout');
    Route::get('me','AuthController@getAuthenticatedUser');
    Route::get('users/all','AuthController@getAllUserInfo');
    });

    
    Route::group(['namespace' => 'App\Http\Controllers\API','prefix' => '/admin'], function () {

        //Dashboard Data
        Route::get('dashboard','DashboardController@index');

        //Patient Route
        Route::get('patient','PatientController@index');
        Route::post('patient','PatientController@store');
        Route::get('patient/search','PatientController@search');
        Route::get('patient/{id}','PatientController@edit');
        Route::post('patient/update/{id}','PatientController@update');
        Route::delete('patient/{id}','PatientController@destroy');

        //Doctor Route
        Route::get('doctor','DoctorController@index');
        Route::post('doctor','DoctorController@store');
        Route::get('doctor/search','DoctorController@search');
        Route::get('doctor/{id}','DoctorController@edit');
        Route::post('doctor/update/{id}','DoctorController@update');
        Route::delete('doctor/{id}','DoctorController@destroy');
        Route::get('/doctor/retrieve/data','DoctorController@retrieve');

        //Department Route
        Route::get('department','Department@index');
        Route::get('department/{id}','Department@edit');
        Route::post('department','Department@store');
        Route::post('department/update/{id}','Department@update');
        Route::delete('department/{id}','Department@destroy');

        //Diagnostics Route
        Route::get('diagnosis','Diagnosis@index');
        Route::post('diagnosis','Diagnosis@store');
        Route::get('diagnosis/search','Diagnosis@search');
        Route::get('diagnosis/{id}','Diagnosis@edit');
        Route::post('diagnosis/update/{id}','Diagnosis@update');
        Route::delete('diagnosis/{id}','Diagnosis@destroy');

        //Appointment Route
        Route::get('appointment','Appointment@index');
        Route::post('appointment','Appointment@store');
        Route::get('appointment/search','Appointment@search');
        Route::get('appointment/{id}','Appointment@edit');
        Route::post('appointment/update/{id}','Appointment@update');
        Route::delete('appointment/{id}','Appointment@destroy');
    });

    Route::group(['namespace' => 'App\Http\Controllers\ThirdParty','prefix' => '/medic'], function () {
      //Third Party API
      Route::get('symptoms','ApiController@symptoms');
      Route::get('issues','ApiController@issues');
      Route::get('diagnosis','ApiController@diagnosis');
      Route::get('authenticate','ApiController@Authenticate');


    });

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
