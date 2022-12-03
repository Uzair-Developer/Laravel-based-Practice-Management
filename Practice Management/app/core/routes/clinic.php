<?php

Route::get('clinics',[
    'as'=>'clinics',
    'uses'=> 'ClinicController@index'
]);

Route::get('clinic/add',[
    'as'=>'addClinic',
    'uses'=> 'ClinicController@addClinic'
]);

Route::post('clinic/add',[
    'as'=>'createClinic',
    'uses'=> 'ClinicController@createClinic'
]);

Route::get('clinic/edit/{id}',[
    'as'=>'editClinic',
    'uses'=> 'ClinicController@editClinic'
]);

Route::post('clinic/edit/{id}',[
    'as'=>'updateClinic',
    'uses'=> 'ClinicController@updateClinic'
]);

Route::get('clinic/delete/{id}',[
    'as'=>'deleteClinic',
    'uses'=> 'ClinicController@deleteClinic'
]);

Route::post('clinic/get-clinics-by-hospital-id',[
    'as'=>'getClinicsByHospitalId',
    'uses'=> 'ClinicController@getClinicsByHospitalId'
]);

Route::get('clinic/get-clinic-availability',[
    'as'=>'getClinicAvailability',
    'uses'=> 'ClinicController@getClinicAvailability'
]);

Route::post('clinic/get-availability-by-clinic-id',[
    'as'=>'getAvailabilityByClinicId',
    'uses'=> 'ClinicController@getAvailabilityByClinicId'
]);
Route::post('clinic/print-excel',[
    'as'=>'printExcelClinics',
    'uses'=> 'ClinicController@printExcelClinics'
]);