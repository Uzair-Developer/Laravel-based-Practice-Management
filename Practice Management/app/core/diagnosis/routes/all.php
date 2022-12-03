<?php

Route::get('auto-complete/disease', [
    'as' => 'autoCompleteDisease',
    'uses' => 'AjaxController@autoCompleteDisease'
]);

Route::get('auto-complete/symptom', [
    'as' => 'autoCompleteSymptom',
    'uses' => 'AjaxController@autoCompleteSymptom'
]);

Route::get('auto-complete/country', [
    'as' => 'autoCompleteCountry',
    'uses' => 'AjaxController@autoCompleteCountry'
]);