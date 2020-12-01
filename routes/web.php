<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', function () {
    return redirect('home');
});

Route::group(['middleware' => ['auth']], function () {
	Route::get('/home', 'HomeController@index')->name('home');
	Route::get('/add-user', 'HomeController@addClient');
	Route::post('/get-clients', 'HomeController@getClients');
	Route::post('/create-client', 'HomeController@createClient');
    Route::post('/update-client', 'HomeController@updateClient');
    Route::get('/edit-user/{id}', 'HomeController@editClient');
    Route::get('/delete-user/{id}', 'HomeController@deleteClient');

	Route::get('/get-status-info', 'HomeController@getStatusInfo');

	Route::get('/parameter', 'ParameterController@index')->name('parameter');
    Route::post('/get-parameters', 'ParameterController@getParameters');

    Route::group(['middleware' => ['admin']], function () {
        Route::get('/add-parameter', 'ParameterController@addParameter');
        Route::post('/create-parameter', 'ParameterController@createParameter');
        Route::post('/update-parameter', 'ParameterController@updateParameter');
        Route::get('/edit-parameter/{id}', 'ParameterController@editParameter');
        Route::get('/delete-parameter/{id}', 'ParameterController@deleteParameter');

        Route::get('/admin', 'AdminController@index')->name('admin');
        Route::get('/add-admin', 'AdminController@addAdmin');
        Route::post('/get-admins', 'AdminController@getAdmins');
        Route::post('/create-admin', 'AdminController@createAdmin');
        Route::post('/update-admin', 'AdminController@updateAdmin');
        Route::get('/edit-admin/{id}', 'AdminController@editAdmin');
        Route::get('/delete-admin/{id}', 'AdminController@deleteAdmin');
    });

});
