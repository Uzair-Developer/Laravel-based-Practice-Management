<?php

Route::get('physician-schedule-exceptions',[
    'as'=>'listPhysicianScheduleException',
    'uses'=> 'PhysicianScheduleExceptionController@listPhysicianScheduleException'
]);

Route::get('physician-schedule-exceptions/manage',[
    'as'=>'managePhysicianScheduleException',
    'uses'=> 'PhysicianScheduleExceptionController@managePhysicianScheduleException'
]);

Route::post('physician-schedule-exceptions/manage',[
    'as'=>'postManagePhysicianScheduleException',
    'uses'=> 'PhysicianScheduleExceptionController@postManagePhysicianScheduleException'
]);


Route::get('physician-schedule-exceptions/delete/{id}',[
    'as'=>'deletePhysicianScheduleException',
    'uses'=> 'PhysicianScheduleExceptionController@deletePhysicianScheduleException'
]);

Route::post('physician-schedule-exceptions/get-date-time',[
    'as'=>'getPhysicianScheduleExceptionsDateTime',
    'uses'=> 'PhysicianScheduleExceptionController@getPhysicianScheduleExceptionsDateTime'
]);

Route::post('physician-schedule-exceptions/update',[
    'as'=>'updatePhysicianScheduleException',
    'uses'=> 'PhysicianScheduleExceptionController@updatePhysicianScheduleException'
]);

Route::post('physician-schedule-exceptions/get-schedule-with-date',[
    'as'=>'getScheduleWithDate',
    'uses'=> 'PhysicianScheduleExceptionController@getScheduleWithDate'
]);