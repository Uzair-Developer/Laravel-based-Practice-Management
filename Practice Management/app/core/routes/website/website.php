<?php

Route::get('website-settings', [
    'as' => 'websiteSettings',
    'uses' => 'WebsiteController@websiteSettings'
]);

Route::post('website-settings', [
    'as' => 'updateWebsiteSettings',
    'uses' => 'WebsiteController@updateWebsiteSettings'
]);

Route::get('online-survey/{lang}/{reservation_id}', [
    'as' => 'websiteOnlineSurvey',
    'uses' => 'WebsiteController@websiteOnlineSurvey'
]);

Route::get('inp-survey/{lang}', [
    'as' => 'websiteInPatientSurvey',
    'uses' => 'WebsiteController@websiteInPatientSurvey'
]);

Route::post('inp-survey/check-in-patient-discharge', [
    'as' => 'websiteCheckInPatientDischarge',
    'uses' => 'WebsiteController@websiteCheckInPatientDischarge'
]);

Route::post('inp-survey/save-survey', [
    'as' => 'webSiteSaveInPatientSurvey',
    'uses' => 'WebsiteController@webSiteSaveInPatientSurvey'
]);