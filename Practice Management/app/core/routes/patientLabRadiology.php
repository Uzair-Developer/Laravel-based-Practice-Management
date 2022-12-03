<?php

Route::get('patient-lab-radiology', [
    'as' => 'listPatientLapRadiology',
    'uses' => 'PatientLapRadiologyController@listPatientLapRadiology'
]);

Route::post('patient-lab-radiology/get-order', [
    'as' => 'getPatientOrderLapRadiology',
    'uses' => 'PatientLapRadiologyController@getPatientOrderLapRadiology'
]);

Route::get('patient-lab-radiology/reset-patient-password/{id}', [
    'as' => 'resetPatientPassword',
    'uses' => 'PatientLapRadiologyController@resetPatientPassword'
]);

Route::get('patient-lab-radiology/change-send-lab-sms/{id}', [
    'as' => 'changeSendLabSMS',
    'uses' => 'PatientLapRadiologyController@changeSendLabSMS'
]);

Route::post('patient-lab-radiology/edit-patient-phone', [
    'as' => 'editPatientPhone',
    'uses' => 'PatientLapRadiologyController@editPatientPhone'
]);