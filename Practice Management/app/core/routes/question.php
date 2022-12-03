<?php
Route::get('question', [
    'as' => 'listQuestion',
    'uses' => 'QuestionController@listQuestion'
]);

Route::post('question/add', [
    'as' => 'createQuestion',
    'uses' => 'QuestionController@createQuestion'
]);


Route::post('question/edit', [
    'as' => 'updateQuestion',
    'uses' => 'QuestionController@updateQuestion'
]);

Route::get('question/delete/{id}', [
    'as' => 'deleteQuestion',
    'uses' => 'QuestionController@deleteQuestion'
]);


Route::post('question/get', [
    'as' => 'getQuestion',
    'uses' => 'QuestionController@getQuestion'
]);

Route::post('question/get-by-survey-html', [
    'as' => 'getQuestionBySurvey',
    'uses' => 'QuestionController@getQuestionBySurvey'
]);

Route::post('question/get-answer-by-question', [
    'as' => 'getAnswerByQuestion',
    'uses' => 'QuestionController@getAnswerByQuestion'
]);