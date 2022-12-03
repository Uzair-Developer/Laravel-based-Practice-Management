<?php

Route::get('public-holidays',[
    'as'=>'publicHoliday',
    'uses'=> 'PublicHolidayController@index'
]);

Route::get('public-holiday/add',[
    'as'=>'addPublicHoliday',
    'uses'=> 'PublicHolidayController@addPublicHoliday'
]);

Route::post('public-holiday/add',[
    'as'=>'createPublicHoliday',
    'uses'=> 'PublicHolidayController@createPublicHoliday'
]);

Route::get('public-holiday/edit/{id}',[
    'as'=>'editPublicHoliday',
    'uses'=> 'PublicHolidayController@editPublicHoliday'
]);

Route::post('public-holiday/edit/{id}',[
    'as'=>'updatePublicHoliday',
    'uses'=> 'PublicHolidayController@updatePublicHoliday'
]);

Route::get('public-holiday/delete/{id}',[
    'as'=>'deletePublicHoliday',
    'uses'=> 'PublicHolidayController@deletePublicHoliday'
]);

Route::get('public-holiday/change-status/{id}',[
    'as'=>'changeStatusPublicHoliday',
    'uses'=> 'PublicHolidayController@changeStatusPublicHoliday'
]);