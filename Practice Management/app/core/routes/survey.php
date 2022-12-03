<?php
Route::get('survey', [
    'as' => 'listSurvey',
    'uses' => 'SurveyController@listSurvey'
]);

Route::get('survey/add', [
    'as' => 'addSurvey',
    'uses' => 'SurveyController@addSurvey'
]);

Route::post('survey/add', [
    'as' => 'createSurvey',
    'uses' => 'SurveyController@createSurvey'
]);

Route::get('survey/edit/{id}',[
    'as'=>'editSurvey',
    'uses'=> 'SurveyController@editSurvey'
]);

Route::post('survey/edit/{id}', [
    'as' => 'updateSurvey',
    'uses' => 'SurveyController@updateSurvey'
]);

Route::get('survey/delete/{id}', [
    'as' => 'deleteSurvey',
    'uses' => 'SurveyController@deleteSurvey'
]);


Route::post('survey/get', [
    'as' => 'getSurvey',
    'uses' => 'SurveyController@getSurvey'
]);