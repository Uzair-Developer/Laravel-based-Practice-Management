<?php

Route::get('ip-to-room',[
    'as'=>'ipToRoom',
    'uses'=> 'IpToRoomController@ipToRoom'
]);

Route::get('ip-to-room/add',[
    'as'=>'addIpToRoom',
    'uses'=> 'IpToRoomController@addIpToRoom'
]);

Route::post('ip-to-room/add',[
    'as'=>'createIpToRoom',
    'uses'=> 'IpToRoomController@createIpToRoom'
]);

Route::get('ip-to-room/edit/{id}',[
    'as'=>'editIpToRoom',
    'uses'=> 'IpToRoomController@editIpToRoom'
]);

Route::post('ip-to-room/edit/{id}',[
    'as'=>'updateIpToRoom',
    'uses'=> 'IpToRoomController@updateIpToRoom'
]);

Route::get('ip-to-room/delete/{id}',[
    'as'=>'deleteIpToRoom',
    'uses'=> 'IpToRoomController@deleteIpToRoom'
]);

Route::post('ip-to-room/get-by-hospital-id',[
    'as'=>'getRoomsByHospitalId',
    'uses'=> 'IpToRoomController@getRoomsByHospitalId'
]);