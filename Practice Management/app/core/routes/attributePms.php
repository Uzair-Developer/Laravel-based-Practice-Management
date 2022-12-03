<?php

Route::get('attribute-pms', [
    'as' => 'listAttributePms',
    'uses' => 'AttributePmsController@listAttributePms'
]);

Route::post('attribute-pms/add', [
    'as' => 'createAttributePms',
    'uses' => 'AttributePmsController@createAttributePms'
]);


Route::post('attribute-pms/edit', [
    'as' => 'updateAttributePms',
    'uses' => 'AttributePmsController@updateAttributePms'
]);

Route::get('attribute-pms/delete/{id}', [
    'as' => 'deleteAttributePms',
    'uses' => 'AttributePmsController@deleteAttributePms'
]);


Route::post('attribute-pms/get', [
    'as' => 'getAttributePms',
    'uses' => 'AttributePmsController@getAttributePms'
]);

Route::post('attribute-pms/get-child-referred-to', [
    'as' => 'getChildReferredTo',
    'uses' => 'AttributePmsController@getChildReferredTo'
]);