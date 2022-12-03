<?php

Route::get('pms-diagnosis',[
    'as'=>'pmsDiagnosis',
    'uses'=> 'PmsDiagnosisController@pmsDiagnosis'
]);

Route::get('pms-diagnosis/add',[
    'as'=>'addPmsDiagnosis',
    'uses'=> 'PmsDiagnosisController@addPmsDiagnosis'
]);

Route::post('pms-diagnosis/add',[
    'as'=>'createPmsDiagnosis',
    'uses'=> 'PmsDiagnosisController@createPmsDiagnosis'
]);

Route::get('pms-diagnosis/edit/{id}',[
    'as'=>'editPmsDiagnosis',
    'uses'=> 'PmsDiagnosisController@editPmsDiagnosis'
]);

Route::post('pms-diagnosis/edit/{id}',[
    'as'=>'updatePmsDiagnosis',
    'uses'=> 'PmsDiagnosisController@updatePmsDiagnosis'
]);

Route::get('pms-diagnosis/delete/{id}',[
    'as'=>'deletePmsDiagnosis',
    'uses'=> 'PmsDiagnosisController@deletePmsDiagnosis'
]);

Route::get('pms-diagnosis/get-disease-by-name',[
    'as'=>'getDiseaseByName',
    'uses'=> 'PmsDiagnosisController@getDiseaseByName'
]);