<?php

Route::get('start-diagnosis', [
    'as' => 'startDiagnosis1',
    'uses' => 'DiagnosisController@startDiagnosis1'
]);

Route::post('start-diagnosis', [
    'as' => 'postStartDiagnosis1',
    'uses' => 'DiagnosisController@postStartDiagnosis1'
]);

Route::get('start-diagnosis/step2', [
    'as' => 'startDiagnosis2',
    'uses' => 'DiagnosisController@startDiagnosis2'
]);

Route::post('start-diagnosis/step2', [
    'as' => 'postStartDiagnosis2',
    'uses' => 'DiagnosisController@postStartDiagnosis2'
]);

Route::get('start-diagnosis/step3', [
    'as' => 'startDiagnosis3',
    'uses' => 'DiagnosisController@startDiagnosis3'
]);

Route::post('start-diagnosis/step3', [
    'as' => 'postStartDiagnosis3',
    'uses' => 'DiagnosisController@postStartDiagnosis3'
]);

Route::get('start-diagnosis/step4', [
    'as' => 'startDiagnosis4',
    'uses' => 'DiagnosisController@startDiagnosis4'
]);

Route::post('start-diagnosis/step4', [
    'as' => 'postStartDiagnosis4',
    'uses' => 'DiagnosisController@postStartDiagnosis4'
]);

Route::post('start-diagnosis/step2/get-disease', [
    'as' => 'getDiseaseOfSymptoms',
    'uses' => 'DiagnosisController@getDiseaseOfSymptoms'
]);

Route::post('start-diagnosis/step1/check-patient-phone', [
    'as' => 'checkPatientExist',
    'uses' => 'DiagnosisController@checkPatientExist'
]);

Route::post('start-diagnosis/step2/create-symptom', [
    'as' => 'createSymptomInDiagnosis',
    'uses' => 'DiagnosisController@createSymptomInDiagnosis'
]);

Route::post('start-diagnosis/step2/add-disease-to-symptom', [
    'as' => 'addDiseaseToSymptom',
    'uses' => 'DiagnosisController@addDiseaseToSymptom'
]);

Route::post('start-diagnosis/step2/add-comment-to-symptom', [
    'as' => 'addCommentToSymptom',
    'uses' => 'DiagnosisController@addCommentToSymptom'
]);

Route::post('start-diagnosis/comment/delete', [
    'as' => 'diagnosisDeleteComment',
    'uses' => 'DiagnosisController@diagnosisDeleteComment'
]);

Route::post('start-diagnosis/disease-symptom/delete', [
    'as' => 'diagnosisDeleteDiseaseSymptom',
    'uses' => 'DiagnosisController@diagnosisDeleteDiseaseSymptom'
]);

Route::post('start-diagnosis/start-again', [
    'as' => 'diagnosisStartAgain',
    'uses' => 'DiagnosisController@diagnosisStartAgain'
]);
