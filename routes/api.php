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

Route::namespace('\App\Http\Controllers\Auth')->prefix('auth')->group(function () {
    Route::post('register', 'RegisterController@register');

    Route::post('login', 'LoginController@login');
    Route::post('logout', 'LoginController@logout')->middleware(['auth:api'])->name('logout');

    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'ResetPasswordController@reset')->name('password.reset');

    Route::middleware('auth:api')->get('/user', 'AuthController@getUser');

    Route::middleware(['auth:api'])->post('update-password', 'AuthController@updatePassword');
    Route::middleware(['auth:api'])->post('update-settings', 'AuthController@updateSettings');
    Route::middleware(['auth:api'])->post('reorder-pages', 'AuthController@reorderPages');
    Route::middleware('auth:api')->post('/edit', 'AuthController@updateProfile');


});

Route::middleware('api')->post('/users', 'Api\UserController@users');
Route::middleware('auth:api')->post('/load-chats', 'Api\UserController@loadChats');
Route::middleware('auth:api')->post('/load-chat', 'Api\UserController@loadChat');
Route::middleware('auth:api')->post('/start-chat', 'Api\UserController@startChat');
Route::middleware('auth:api')->post('/send-message', 'Api\UserController@sendMessage');
Route::middleware('auth:api')->post('/create-room', 'Api\UserController@createRoom');
Route::middleware('auth:api')->post('/add-room-user', 'Api\UserController@addRoomUser');
