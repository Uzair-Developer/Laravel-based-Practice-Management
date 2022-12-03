<?php

Route::get('organs', [
    'as' => 'listOrgan',
    'uses' => 'OrganController@index'
]);

Route::get('organs/add', [
    'as' => 'addOrgan',
    'uses' => 'OrganController@addOrgan'
]);

Route::post('organs/add', [
    'as' => 'createOrgan',
    'uses' => 'OrganController@createOrgan'
]);

Route::get('organs/edit/{id}', [
    'as' => 'editOrgan',
    'uses' => 'OrganController@editOrgan'
]);

Route::post('organs/edit/{id}', [
    'as' => 'updateOrgan',
    'uses' => 'OrganController@updateOrgan'
]);

Route::get('organs/delete/{id}', [
    'as' => 'deleteOrgan',
    'uses' => 'OrganController@deleteOrgan'
]);
