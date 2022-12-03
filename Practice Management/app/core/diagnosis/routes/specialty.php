<?php

Route::get('specialties', [
    'as' => 'listSpecialty',
    'uses' => 'SpecialtyController@index'
]);

Route::get('specialty/add', [
    'as' => 'addSpecialty',
    'uses' => 'SpecialtyController@addSpecialty'
]);

Route::post('specialty/add', [
    'as' => 'createSpecialty',
    'uses' => 'SpecialtyController@createSpecialty'
]);

Route::get('specialty/edit/{id}', [
    'as' => 'editSpecialty',
    'uses' => 'SpecialtyController@editSpecialty'
]);

Route::post('specialty/edit/{id}', [
    'as' => 'updateSpecialty',
    'uses' => 'SpecialtyController@updateSpecialty'
]);

Route::get('specialty/delete/{id}', [
    'as' => 'deleteSpecialty',
    'uses' => 'SpecialtyController@deleteSpecialty'
]);
