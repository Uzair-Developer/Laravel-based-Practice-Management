<?php

Route::get('pharmacy/list', [
    'as' => 'pharmacyList',
    'uses' => 'PharmacyController@pharmacyList'
]);

Route::post('pharmacy/get-queue-counts', [
    'as' => 'getQueuePharmacyCounts',
    'uses' => 'PharmacyController@getQueuePharmacyCounts'
]);

Route::get('pharmacy/get-next-queue', [
    'as' => 'getNextQueuePharmacy',
    'uses' => 'PharmacyController@getNextQueuePharmacy'
]);

Route::get('pharmacy/call-done/{id}', [
    'as' => 'callDonePharmacyQueue',
    'uses' => 'PharmacyController@callDonePharmacyQueue'
]);

Route::get('pharmacy/patient-pass/{id}', [
    'as' => 'patientPassPharmacyQueue',
    'uses' => 'PharmacyController@patientPassPharmacyQueue'
]);

Route::post('pharmacy/check-next-queue', [
    'as' => 'checkNextQueuePharmacy',
    'uses' => 'PharmacyController@checkNextQueuePharmacy'
]);

Route::post('pharmacy/refresh-queue-pass', [
    'as' => 'refreshQueuePass',
    'uses' => 'PharmacyController@refreshQueuePass'
]);

Route::get('pharmacy/call-from-pass', [
    'as' => 'callFromPassPharmacyQueue',
    'uses' => 'PharmacyController@callFromPassPharmacyQueue'
]);

Route::post('pharmacy/get-activity', [
    'as' => 'pharmacyGetActivity',
    'uses' => 'PharmacyController@pharmacyGetActivity'
]);

Route::post('pharmacy/change-activity', [
    'as' => 'pharmacyChangeActivity',
    'uses' => 'PharmacyController@pharmacyChangeActivity'
]);

Route::get('pharmacy/cancel-patient/{id}', [
    'as' => 'cancelPatientPharmacyQueue',
    'uses' => 'PharmacyController@cancelPatientPharmacyQueue'
]);