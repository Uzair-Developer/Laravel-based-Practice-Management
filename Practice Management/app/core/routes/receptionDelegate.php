<?php

Route::get('reception-delegate',[
    'as'=>'receptionDelegate',
    'uses'=> 'ReceptionDelegateController@receptionDelegate'
]);

Route::get('reception-delegate/add',[
    'as'=>'addReceptionDelegate',
    'uses'=> 'ReceptionDelegateController@addReceptionDelegate'
]);

Route::post('reception-delegate/add',[
    'as'=>'createReceptionDelegate',
    'uses'=> 'ReceptionDelegateController@createReceptionDelegate'
]);

Route::get('reception-delegate/edit/{id}',[
    'as'=>'editReceptionDelegate',
    'uses'=> 'ReceptionDelegateController@editReceptionDelegate'
]);

Route::post('reception-delegate/edit/{id}',[
    'as'=>'updateReceptionDelegate',
    'uses'=> 'ReceptionDelegateController@updateReceptionDelegate'
]);

Route::get('reception-delegate/delete/{id}',[
    'as'=>'deleteReceptionDelegate',
    'uses'=> 'ReceptionDelegateController@deleteReceptionDelegate'
]);