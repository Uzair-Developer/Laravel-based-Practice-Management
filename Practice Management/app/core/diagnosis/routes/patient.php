<?php

Route::get('patients', [
    'as' => 'listPatient',
    'uses' => 'PatientController@listPatient'
]);

Route::post('patient/events', [
    'as' => 'listEvents',
    'uses' => 'PatientController@listEvents'
]);

Route::get('patient/delete/{id}', [
    'as' => 'deletePatient',
    'uses' => 'PatientController@deletePatient'
]);

Route::get('patient/edit/{id}', [
    'as' => 'editPatient',
    'uses' => 'PatientController@editPatient'
]);

Route::post('patient/edit/{id}', [
    'as' => 'updatePatient',
    'uses' => 'PatientController@updatePatient'
]);

Route::post('patient/get-info',[
    'as'=>'showReserveBtn',
    'uses'=> 'PatientController@showReserveBtn'
]);

Route::post('patient/get-caller-info',[
    'as'=>'searchCallerPhone',
    'uses'=> 'PatientController@searchCallerPhone'
]);

Route::post('patient/get-caller-info-with-patient',[
    'as'=>'searchCallerPhoneWithPatientId',
    'uses'=> 'PatientController@searchCallerPhoneWithPatientId'
]);

Route::post('patient/updated-patient-data',[
    'as'=>'updatePatientData',
    'uses'=> 'PatientController@updatePatientData'
]);

Route::post('patient/get-data',[
    'as'=>'getPatientData',
    'uses'=> 'PatientController@getPatientData'
]);

