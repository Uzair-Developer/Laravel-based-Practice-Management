<?php

Route::get('ip-to-reception',[
    'as'=>'ipToReception',
    'uses'=> 'IpToReceptionController@ipToReception'
]);

Route::get('ip-to-reception/add',[
    'as'=>'addIpToReception',
    'uses'=> 'IpToReceptionController@addIpToReception'
]);

Route::post('ip-to-reception/add',[
    'as'=>'createIpToReception',
    'uses'=> 'IpToReceptionController@createIpToReception'
]);

Route::get('ip-to-reception/edit/{id}',[
    'as'=>'editIpToReception',
    'uses'=> 'IpToReceptionController@editIpToReception'
]);

Route::post('ip-to-reception/edit/{id}',[
    'as'=>'updateIpToReception',
    'uses'=> 'IpToReceptionController@updateIpToReception'
]);

Route::get('ip-to-reception/delete/{id}',[
    'as'=>'deleteIpToReception',
    'uses'=> 'IpToReceptionController@deleteIpToReception'
]);

Route::post('ip-to-reception/get-by-hospital-id',[
    'as'=>'getReceptionByHospitalId',
    'uses'=> 'IpToReceptionController@getReceptionByHospitalId'
]);