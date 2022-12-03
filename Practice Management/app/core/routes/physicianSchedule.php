<?php

Route::get('physician-schedules', [
    'as' => 'physicianSchedules',
    'uses' => 'PhysicianScheduleController@index'
]);

Route::get('physician-schedules/add', [
    'as' => 'addPhysicianSchedule',
    'uses' => 'PhysicianScheduleController@addPhysicianSchedule'
]);

Route::post('physician-schedules/add', [
    'as' => 'createPhysicianSchedule',
    'uses' => 'PhysicianScheduleController@createPhysicianSchedule'
]);

Route::get('physician-schedules/edit/{id}', [
    'as' => 'editPhysicianSchedule',
    'uses' => 'PhysicianScheduleController@editPhysicianSchedule'
]);

Route::post('physician-schedules/edit/{id}', [
    'as' => 'updatePhysicianSchedule',
    'uses' => 'PhysicianScheduleController@updatePhysicianSchedule'
]);

Route::get('physician-schedules/delete/{id}', [
    'as' => 'deletePhysicianSchedule',
    'uses' => 'PhysicianScheduleController@deletePhysicianSchedule'
]);

Route::post('physician-schedules/get-physician-by-clinic', [
    'as' => 'getPhysicianByClinic',
    'uses' => 'PhysicianScheduleController@getPhysicianByClinic'
]);

Route::post('physician-schedules/get-physician-schedule-by-clinic-schedule', [
    'as' => 'getPhysicianScheduleByClinicSchedule',
    'uses' => 'PhysicianScheduleController@getPhysicianScheduleByClinicSchedule'
]);

Route::post('physician-schedules/get-physician-schedule-by-id', [
    'as' => 'getPhysicianScheduleView',
    'uses' => 'PhysicianScheduleController@getPhysicianScheduleView'
]);


Route::get('physician-schedules/import-excel', [
    'as' => 'importExcelPhysicianSchedule',
    'uses' => 'PhysicianScheduleController@importExcelPhysicianSchedule'
]);

Route::post('physician-schedules/import-excel', [
    'as' => 'postImportExcelPhysicianSchedule',
    'uses' => 'PhysicianScheduleController@postImportExcelPhysicianSchedule'
]);

Route::post('physician-schedules/download-excel', [
    'as' => 'downloadExcelPhysicianSchedule',
    'uses' => 'PhysicianScheduleController@downloadExcelPhysicianSchedule'
]);

Route::post('physician-schedules/get-physician-schedule-by-physician-id', [
    'as' => 'getPhysicianScheduleByPhysicianId',
    'uses' => 'PhysicianScheduleController@getPhysicianScheduleByPhysicianId'
]);

Route::post('physician-schedules/get-physician-schedule', [
    'as' => 'getPhysicianSchedule',
    'uses' => 'PhysicianScheduleController@getPhysicianSchedule'
]);

Route::get('physician-schedules/change-status-physician-schedule/{id}', [
    'as' => 'changeStatusPhysicianSchedule',
    'uses' => 'PhysicianScheduleController@changeStatusPhysicianSchedule'
]);

Route::post('physician-schedules/change-dates-physician-schedule', [
    'as' => 'changeDatePhysicianSchedule',
    'uses' => 'PhysicianScheduleController@changeDatePhysicianSchedule'
]);

Route::post('physician-schedules/change-status-physician-schedule-array', [
    'as' => 'changeStatusPhysicianScheduleArray',
    'uses' => 'PhysicianScheduleController@changeStatusPhysicianScheduleArray'
]);

Route::post('physician-schedules/delete-physician-schedule-array', [
    'as' => 'deletePhysicianScheduleArray',
    'uses' => 'PhysicianScheduleController@deletePhysicianScheduleArray'
]);