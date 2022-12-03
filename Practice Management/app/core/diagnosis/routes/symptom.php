<?php

Route::get('symptoms', [
    'as' => 'listSymptom',
    'uses' => 'SymptomController@index'
]);

Route::get('symptom/add', [
    'as' => 'addSymptom',
    'uses' => 'SymptomController@addSymptom'
]);

Route::post('symptom/add', [
    'as' => 'createSymptom',
    'uses' => 'SymptomController@createSymptom'
]);

Route::get('symptom/edit/{id}', [
    'as' => 'editSymptom',
    'uses' => 'SymptomController@editSymptom'
]);

Route::post('symptom/edit/{id}', [
    'as' => 'updateSymptom',
    'uses' => 'SymptomController@updateSymptom'
]);

Route::get('symptom/delete/{id}', [
    'as' => 'deleteSymptom',
    'uses' => 'SymptomController@deleteSymptom'
]);
