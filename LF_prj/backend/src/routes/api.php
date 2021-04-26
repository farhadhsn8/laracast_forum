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


Route::prefix('v1/')->group(function(){
    Route::prefix('auth')->group(function(){
        Route::post('/register','API\v01\Auth\AuthController@register')->name('auth.register');
        Route::post('/login','API\v01\Auth\AuthController@login')->name('auth.login');
        Route::post('/user','API\v01\Auth\AuthController@user')->name('auth.user');
        Route::post('/logout','API\v01\Auth\AuthController@logout')->name('auth.logout');
    });

    Route::prefix('/channel')->group(function(){
        Route::get('/all','API\v01\Channel\ChannelController@getAllChannels')->name('channel.all');
        Route::post('/create','API\v01\Channel\ChannelController@createNewChannel')->name('channel.create');
        Route::put('/update','API\v01\Channel\ChannelController@editChannel')->name('channel.edit');


    });

});
