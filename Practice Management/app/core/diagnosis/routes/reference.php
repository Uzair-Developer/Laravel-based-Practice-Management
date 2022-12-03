<?php

Route::get('references', [
    'as' => 'listReference',
    'uses' => 'ReferenceController@listReference'
]);

Route::get('references/add', [
    'as' => 'addReference',
    'uses' => 'ReferenceController@addReference'
]);

Route::post('references/add', [
    'as' => 'createReference',
    'uses' => 'ReferenceController@createReference'
]);

Route::get('references/edit/{id}', [
    'as' => 'editReference',
    'uses' => 'ReferenceController@editReference'
]);

Route::post('references/edit/{id}', [
    'as' => 'updateReference',
    'uses' => 'ReferenceController@updateReference'
]);

Route::get('references/delete/{id}', [
    'as' => 'deleteReference',
    'uses' => 'ReferenceController@deleteReference'
]);
