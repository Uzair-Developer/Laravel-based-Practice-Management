<?php
Route::get('patient-survey', [
    'as' => 'listPatientSurvey',
    'uses' => 'PatientSurveyController@listPatientSurvey'
]);

Route::get('patient-survey/view', [
    'as' => 'viewPatientSurvey',
    'uses' => 'PatientSurveyController@viewPatientSurvey'
]);

Route::get('patient-survey/report/counts', [
    'as' => 'reportCountsPatientSurvey',
    'uses' => 'PatientSurveyController@reportCountsPatientSurvey'
]);
Route::post('patient-survey/report/counts/print-excel', [
    'as' => 'printExcelPatientSurveyReportCounts',
    'uses' => 'PatientSurveyController@printExcelPatientSurveyReportCounts'
]);