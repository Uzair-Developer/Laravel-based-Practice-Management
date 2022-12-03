<?php
Route::get('answer-type', [
    'as' => 'listAnswerType',
    'uses' => 'AnswerTypeController@listAnswerType'
]);

Route::post('answer-type/add', [
    'as' => 'createAnswerType',
    'uses' => 'AnswerTypeController@createAnswerType'
]);


Route::post('answer-type/edit', [
    'as' => 'updateAnswerType',
    'uses' => 'AnswerTypeController@updateAnswerType'
]);

Route::get('answer-type/delete/{id}', [
    'as' => 'deleteAnswerType',
    'uses' => 'AnswerTypeController@deleteAnswerType'
]);

Route::post('answer-type/get', [
    'as' => 'getAnswerType',
    'uses' => 'AnswerTypeController@getAnswerType'
]);