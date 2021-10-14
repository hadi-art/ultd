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

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// User route group
Route::group(['prefix'=>'user','middleware'=>['auth','permission:system.user']],function(){
    Route::get('/', 'UserController@index')->name('user.index');
    Route::get('/create', 'UserController@create')->name('user.create')->middleware('permission:system.user.create');
    Route::post('/store', 'UserController@store')->name('user.store')->middleware('permission:system.user.create');

    Route::get('/{id}/edit','UserController@edit')->name('user.edit')->middleware('permission:system.user');
    Route::put('/{id}/update','UserController@update')->name('user.update')->middleware('permission:system.user');

    Route::get('/{id}/permission','UserController@permission')->name('user.permission')->middleware('permission:system.user.permission');
    Route::put('/{id}/assign/permission','UserController@assignPermission')->name('user.assignPermission')->middleware('permission:system.user.permission');

    Route::delete('/destroy','UserController@destroy')->name('user.delete')->middleware('permission:system.user.destroy');

    Route::get('/change-password','UserController@changePassword')->name('change-password');
    Route::post('/change-password','UserController@changePassword')->name('change-password-save');
});

// Role route group
Route::group(['prefix'=>'role','middleware'=>['auth','permission:system.roles']],function(){//
    Route::get('/', 'RoleController@index')->name('role.index');

    Route::get('/create','RoleController@create')->name('role.create')->middleware('permission:system.roles');
    Route::post('/store','RoleController@store')->name('role.store')->middleware('permission:system.roles');

    Route::get('/{id}/edit','RoleController@edit')->name('role.edit')->middleware('permission:system.roles');
    Route::put('/{id}/update','RoleController@update')->name('role.update')->middleware('permission:system.roles');

    Route::get('/{id}/permission','RoleController@permission')->name('role.permission')->middleware('permission:system.roles');
    Route::put('/{id}/assign/permission','RoleController@assignPermission')->name('role.assignPermission')->middleware('permission:system.roles');


    Route::delete('/destroy','RoleController@destroy')->name('role.destroy')->middleware('permission:system.roles');
});

// Permission route group
Route::group(['prefix'=>'permission','middleware' => ['auth','permission:system.permissions']],function(){ //
    Route::get('/', 'PermissionController@index')->name('permissions');
    Route::get('/all', 'PermissionController@allRecords')->name('permissions.all');
    // Route::get('/create','PermissionController@create')->name('permission.create')->middleware('permission:system.permission.create');
    Route::post('/store','PermissionController@store')->name('permission.store')->middleware('permission:system.permission.create');
    Route::get('/{id}/edit','PermissionController@edit')->name('permission.edit')->middleware('permission:system.permission.edit');
    Route::put('/{id}/update','PermissionController@update')->name('permission.update')->middleware('permission:system.permission.edit');
    Route::delete('/destroy','PermissionController@destroy')->name('permission.destroy')->middleware('permission:system.permission.destroy');
    Route::put('/destroy','PermissionController@recovered')->name('permission.recovered')->middleware('permission:system.permission.destroy');

});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/diah', 'HomeController@diah')->name('diah');
Route::get('/hadi', 'HomeController@hadi')->name('hadi');
