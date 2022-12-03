<?php

Route::get('profile-setting', [
    'as' => 'listPhysicianAttribute',
    'uses' => 'PhysicianAttributeController@listPhysicianAttribute'
]);

Route::post('profile-setting/add', [
    'as' => 'createPhysicianAttribute',
    'uses' => 'PhysicianAttributeController@createPhysicianAttribute'
]);


Route::post('profile-setting/edit', [
    'as' => 'updatePhysicianAttribute',
    'uses' => 'PhysicianAttributeController@updatePhysicianAttribute'
]);

Route::post('profile-setting/delete', [
    'as' => 'deletePhysicianAttribute',
    'uses' => 'PhysicianAttributeController@deletePhysicianAttribute'
]);


Route::post('profile-setting/get', [
    'as' => 'getPhysicianAttribute',
    'uses' => 'PhysicianAttributeController@getPhysicianAttribute'
]);

Route::post('profile-setting/get-by-type', [
    'as' => 'getPhysicianAttributeByType',
    'uses' => 'PhysicianAttributeController@getPhysicianAttributeByType'
]);