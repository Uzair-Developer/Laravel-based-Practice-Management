<?php

Route::get('auto-complete/patient', [
    'as' => 'autoCompletePatient',
    'uses' => 'AjaxController@autoCompletePatient'
]);

Route::get('auto-complete-2/patient', [
    'as' => 'autoCompletePatient2',
    'uses' => 'AjaxController@autoCompletePatient2'
]);

Route::get('auto-complete/patient/all', [
    'as' => 'autoCompletePatientAll',
    'uses' => 'AjaxController@autoCompletePatientAll'
]);

Route::get('auto-complete/patient/show-name', [
    'as' => 'autoCompletePatientShowName',
    'uses' => 'AjaxController@autoCompletePatientShowName'
]);

Route::get('auto-complete/patient/search-by-phone', [
    'as' => 'autoCompletePatientByPhone',
    'uses' => 'AjaxController@autoCompletePatientByPhone'
]);

Route::get('auto-complete/patient/search-by-phone2', [
    'as' => 'autoCompletePatientByPhone2',
    'uses' => 'AjaxController@autoCompletePatientByPhone2'
]);

Route::get('auto-complete/patient/search-by-national-id', [
    'as' => 'autoCompletePatientByNationalId',
    'uses' => 'AjaxController@autoCompletePatientByNationalId'
]);

Route::get('auto-complete/patient/search-by-national-id2', [
    'as' => 'autoCompletePatientByNationalId2',
    'uses' => 'AjaxController@autoCompletePatientByNationalId2'
]);