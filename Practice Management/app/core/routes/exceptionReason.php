<?php

Route::get('exception-reasons', [
    'as' => 'listExceptionReason',
    'uses' => 'ExceptionReasonController@listExceptionReason'
]);

Route::get('exception-reasons/add', [
    'as' => 'addExceptionReason',
    'uses' => 'ExceptionReasonController@addExceptionReason'
]);

Route::post('exception-reasons/add', [
    'as' => 'createExceptionReason',
    'uses' => 'ExceptionReasonController@createExceptionReason'
]);

Route::get('exception-reasons/edit/{id}', [
    'as' => 'editExceptionReason',
    'uses' => 'ExceptionReasonController@editExceptionReason'
]);

Route::post('exception-reasons/edit/{id}', [
    'as' => 'updateExceptionReason',
    'uses' => 'ExceptionReasonController@updateExceptionReason'
]);

Route::get('exception-reasons/delete/{id}', [
    'as' => 'deleteExceptionReason',
    'uses' => 'ExceptionReasonController@deleteExceptionReason'
]);