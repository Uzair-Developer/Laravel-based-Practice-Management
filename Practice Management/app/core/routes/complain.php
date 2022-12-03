<?php

Route::get('complains', [
    'as' => 'listComplain',
    'uses' => 'ComplainController@listComplain'
]);

Route::get('complain/add', [
    'as' => 'addComplain',
    'uses' => 'ComplainController@addComplain'
]);

Route::post('complain/add', [
    'as' => 'createComplain',
    'uses' => 'ComplainController@createComplain'
]);

Route::get('complain/edit/{id}', [
    'as' => 'editComplain',
    'uses' => 'ComplainController@editComplain'
]);

Route::post('complain/edit/{id}', [
    'as' => 'updateComplain',
    'uses' => 'ComplainController@updateComplain'
]);

Route::get('complain/delete/{id}', [
    'as' => 'deleteComplain',
    'uses' => 'ComplainController@deleteComplain'
]);

Route::get('complain/read/{id}', [
    'as' => 'readComplain',
    'uses' => 'ComplainController@readComplain'
]);