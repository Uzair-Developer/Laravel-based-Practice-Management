<?php

Route::get('clinic-schedules',[
    'as'=>'clinicSchedules',
    'uses'=> 'ClinicScheduleController@index'
]);

Route::get('clinic-schedules/add',[
    'as'=>'addClinicSchedule',
    'uses'=> 'ClinicScheduleController@addClinicSchedule'
]);

Route::post('clinic-schedules/add',[
    'as'=>'createClinicSchedule',
    'uses'=> 'ClinicScheduleController@createClinicSchedule'
]);

Route::get('clinic-schedules/edit/{id}',[
    'as'=>'editClinicSchedule',
    'uses'=> 'ClinicScheduleController@editClinicSchedule'
]);

Route::post('clinic-schedules/edit/{id}',[
    'as'=>'updateClinicSchedule',
    'uses'=> 'ClinicScheduleController@updateClinicSchedule'
]);

Route::get('clinic-schedules/delete/{id}',[
    'as'=>'deleteClinicSchedule',
    'uses'=> 'ClinicScheduleController@deleteClinicSchedule'
]);

Route::post('clinic-schedules/duplicate',[
    'as'=>'duplicateClinicSchedule',
    'uses'=> 'ClinicScheduleController@duplicateClinicSchedule'
]);

Route::post('clinic-schedules/get-last-schedule-clinic',[
    'as'=>'getLastScheduleOfClinic',
    'uses'=> 'ClinicScheduleController@getLastScheduleOfClinic'
]);

Route::get('clinic-schedules/import-excel',[
    'as'=>'importExcelClinicSchedule',
    'uses'=> 'ClinicScheduleController@importExcelClinicSchedule'
]);

Route::post('clinic-schedules/import-excel',[
    'as'=>'postImportExcelClinicSchedule',
    'uses'=> 'ClinicScheduleController@postImportExcelClinicSchedule'
]);

Route::post('clinic-schedules/download-excel',[
    'as'=>'downloadExcelClinicSchedule',
    'uses'=> 'ClinicScheduleController@downloadExcelClinicSchedule'
]);

Route::post('clinic-schedules/get-schedule-id',[
    'as'=>'getScheduleId',
    'uses'=> 'ClinicScheduleController@getScheduleId'
]);

Route::post('clinic-schedules/check-date-is-available',[
    'as'=>'checkDateIsAvailable',
    'uses'=> 'ClinicScheduleController@checkDateIsAvailable'
]);