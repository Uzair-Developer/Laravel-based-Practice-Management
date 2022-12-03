<?php

Route::post('get-hospitals', [
    'as' => 'webSiteAPIGetHospitals',
    'uses' => 'WebsiteAPIController@webSiteAPIGetHospitals'
]);

Route::post('get-clinics', [
    'as' => 'webSiteAPIGetClinicByHospital',
    'uses' => 'WebsiteAPIController@webSiteAPIGetClinicByHospital'
]);

Route::post('get-physicians-by-clinic', [
    'as' => 'webSiteAPIGetPhysicianByClinic',
    'uses' => 'WebsiteAPIController@webSiteAPIGetPhysicianByClinic'
]);

Route::post('get-slots-of-physician', [
    'as' => 'webSiteAPIGetSlotsOfPhysician',
    'uses' => 'WebsiteAPIController@webSiteAPIGetSlotsOfPhysician'
]);

Route::post('check-patient', [
    'as' => 'webSiteAPICheckPatient',
    'uses' => 'WebsiteAPIController@webSiteAPICheckPatient'
]);

Route::post('add-reservation', [
    'as' => 'webSiteAPIAddReservation',
    'uses' => 'WebsiteAPIController@webSiteAPIAddReservation'
]);

Route::post('get-patient-instructions', [
    'as' => 'webSiteAPIGetPatientInstructions',
    'uses' => 'WebsiteAPIController@webSiteAPIGetPatientInstructions'
]);

Route::post('api-get-reservation-data', [
    'as' => 'webSiteAPIGetReservationData',
    'uses' => 'WebsiteAPIController@webSiteAPIGetReservationData'
]);

Route::post('api-check-patient-with-reservation', [
    'as' => 'websiteAPICheckPatientWithReservation',
    'uses' => 'WebsiteAPIController@websiteAPICheckPatientWithReservation'
]);

Route::post('api-save-survey', [
    'as' => 'webSiteAPISaveSurvey',
    'uses' => 'WebsiteAPIController@webSiteAPISaveSurvey'
]);

