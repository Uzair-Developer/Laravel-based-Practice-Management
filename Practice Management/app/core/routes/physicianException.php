<?php

Route::get('physician-exceptions',[
    'as'=>'physicianExceptions',
    'uses'=> 'PhysicianExceptionController@index'
]);

Route::get('physician-exceptions/add',[
    'as'=>'addPhysicianException',
    'uses'=> 'PhysicianExceptionController@addPhysicianException'
]);

Route::post('physician-exceptions/add',[
    'as'=>'createPhysicianException',
    'uses'=> 'PhysicianExceptionController@createPhysicianException'
]);

Route::get('physician-exceptions/edit/{id}',[
    'as'=>'editPhysicianException',
    'uses'=> 'PhysicianExceptionController@editPhysicianException'
]);

Route::post('physician-exceptions/edit/{id}',[
    'as'=>'updatePhysicianException',
    'uses'=> 'PhysicianExceptionController@updatePhysicianException'
]);

Route::get('physician-exceptions/delete/{id}',[
    'as'=>'deletePhysicianException',
    'uses'=> 'PhysicianExceptionController@deletePhysicianException'
]);

Route::get('physician-exceptions/approved/{id}',[
    'as'=>'approvedPhysicianException',
    'uses'=> 'PhysicianExceptionController@approvedPhysicianException'
]);
Route::get('physician-exceptions/not-approved/{id}',[
    'as'=>'notApprovedPhysicianException',
    'uses'=> 'PhysicianExceptionController@notApprovedPhysicianException'
]);

Route::post('physician-exceptions/get-by-physician-schedule',[
    'as'=>'getByPhysicianSchedule',
    'uses'=> 'PhysicianExceptionController@getByPhysicianSchedule'
]);


Route::post('physician/get-available-times',[
    'as'=>'getPhysicianAvailableTime',
    'uses'=> 'PhysicianExceptionController@getPhysicianAvailableTime'
]);