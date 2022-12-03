<?php

Route::get('ip-to-screen',[
    'as'=>'ipToScreen',
    'uses'=> 'IpToScreenController@ipToScreen'
]);

Route::get('ip-to-screen/add',[
    'as'=>'addIpToScreen',
    'uses'=> 'IpToScreenController@addIpToScreen'
]);

Route::post('ip-to-screen/add',[
    'as'=>'createIpToScreen',
    'uses'=> 'IpToScreenController@createIpToScreen'
]);

Route::get('ip-to-screen/edit/{id}',[
    'as'=>'editIpToScreen',
    'uses'=> 'IpToScreenController@editIpToScreen'
]);

Route::post('ip-to-screen/edit/{id}',[
    'as'=>'updateIpToScreen',
    'uses'=> 'IpToScreenController@updateIpToScreen'
]);

Route::get('ip-to-screen/delete/{id}',[
    'as'=>'deleteIpToScreen',
    'uses'=> 'IpToScreenController@deleteIpToScreen'
]);