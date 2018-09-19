<?php

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

Route::get('/test', ['uses' => "App\StaticsController@test"]);


Route::get('/', ['uses' => "App\StaticsController@showHome"]);

Route::get('/admin/login', ['uses' => "App\AdminController@showLoginAdmin"]);
//Example de route

Auth::routes();

