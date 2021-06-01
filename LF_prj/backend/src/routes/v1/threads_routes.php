<?php

use Illuminate\Support\Facades\Route;

Route::resource('threads', App\Http\Controllers\API\v1\Thread\ThreadController::class);


Route::prefix('/threads')->group(function () {
    Route::resource('answers', 'App\Http\Controllers\API\v1\Thread\AnswerController');

    Route::post('/{thread}/subscribe' , 'App\Http\Controllers\API\v1\Thread\SubscribeController@subscribe')->name('subscribe');
    Route::post('/{thread}/unsubscribe' , 'App\Http\Controllers\API\v1\Thread\SubscribeController@unSubscribe')->name('unSubscribe');

    ;
});
