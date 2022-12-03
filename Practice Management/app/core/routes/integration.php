<?php

Route::get('get-his-physicians', [
    'as' => 'getHisPhysicians',
    'uses' => 'IntegrationController@getHisPhysicians'
]);

Route::get('get-his-patients', [
    'as' => 'getHisPatient',
    'uses' => 'IntegrationController@getHisPatient'
]);


Route::get('get-ryd-his-patients', [
    'as' => 'getRydHisPatient',
    'uses' => 'IntegrationController@getRydHisPatient'
]);


Route::get('get-patient-lap-radiology', [
    'as' => 'getPatientLabRadiology',
    'uses' => 'IntegrationController@getPatientLabRadiology'
]);