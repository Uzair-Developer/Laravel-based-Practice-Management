<?php

Route::get('diseases', [
    'as' => 'listDisease',
    'uses' => 'DiseaseController@index'
]);

Route::get('disease/add', [
    'as' => 'addDisease',
    'uses' => 'DiseaseController@addDisease'
]);

Route::post('disease/add', [
    'as' => 'createDisease',
    'uses' => 'DiseaseController@createDisease'
]);

Route::get('disease/edit/{id}', [
    'as' => 'editDisease',
    'uses' => 'DiseaseController@editDisease'
]);

Route::post('disease/edit/{id}', [
    'as' => 'updateDisease',
    'uses' => 'DiseaseController@updateDisease'
]);

Route::get('disease/delete/{id}', [
    'as' => 'deleteDisease',
    'uses' => 'DiseaseController@deleteDisease'
]);

Route::post('disease/delete/symptom', [
    'as' => 'deleteDiseaseSymptom',
    'uses' => 'DiseaseController@deleteDiseaseSymptom'
]);

Route::post('disease/delete/question', [
    'as' => 'deleteDiseaseQuestion',
    'uses' => 'DiseaseController@deleteDiseaseQuestion'
]);

Route::post('disease/add/symptom', [
    'as' => 'createSymptomInDisease',
    'uses' => 'DiseaseController@createSymptomInDisease'
]);

Route::get('disease/symptoms/get-pending', [
    'as' => 'diseaseSymptomsPending',
    'uses' => 'DiseaseController@diseaseSymptomsPending'
]);

Route::get('disease/symptoms/approve/{id}', [
    'as' => 'approveRelation',
    'uses' => 'DiseaseController@approveRelation'
]);

Route::get('disease/symptoms/cancel/{id}', [
    'as' => 'cancelRelation',
    'uses' => 'DiseaseController@cancelRelation'
]);

Route::post('disease/symptoms/cancel/{id}', [
    'as' => 'cancelRelationPost',
    'uses' => 'DiseaseController@cancelRelationPost'
]);

Route::post('disease/symptoms/edit', [
    'as' => 'editRelation',
    'uses' => 'DiseaseController@editRelation'
]);
