<?php
Route::get('survey-group', [
    'as' => 'listSurveyGroup',
    'uses' => 'SurveyGroupController@listSurveyGroup'
]);

Route::post('survey-group/add', [
    'as' => 'createSurveyGroup',
    'uses' => 'SurveyGroupController@createSurveyGroup'
]);

Route::post('survey-group/edit', [
    'as' => 'updateSurveyGroup',
    'uses' => 'SurveyGroupController@updateSurveyGroup'
]);

Route::get('survey-group/delete/{id}', [
    'as' => 'deleteSurveyGroup',
    'uses' => 'SurveyGroupController@deleteSurveyGroup'
]);

Route::post('survey-group/get', [
    'as' => 'getSurveyGroup',
    'uses' => 'SurveyGroupController@getSurveyGroup'
]);

Route::get('survey-group/get-all-html', [
    'as' => 'getAllGroupsHtml',
    'uses' => 'SurveyGroupController@getAllGroupsHtml'
]);