<?php

Route::get('queue/list', [
    'as' => 'listQueue',
    'uses' => 'QueueController@listQueue'
]);
Route::post('queue/next', [
    'as' => 'getNextQueue',
    'uses' => 'QueueController@getNextQueue'
]);