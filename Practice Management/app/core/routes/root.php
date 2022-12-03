<?php

Route::get('system-root',[
    'as'=>'systemRoot',
    'uses'=> 'SystemRootController@systemRoot'
]);

Route::post('system-root/edit',[
    'as'=>'updateSystemRoot',
    'uses'=> 'SystemRootController@updateSystemRoot'
]);