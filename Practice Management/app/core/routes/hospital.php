<?php

Route::get('hospitals',[
    'as'=>'hospitals',
    'uses'=> 'HospitalController@index'
]);

Route::get('hospital/add',[
    'as'=>'addHospital',
    'uses'=> 'HospitalController@addHospital'
]);

Route::post('hospital/add',[
    'as'=>'createHospital',
    'uses'=> 'HospitalController@createHospital'
]);

Route::get('hospital/edit/{id}',[
    'as'=>'editHospital',
    'uses'=> 'HospitalController@editHospital'
]);

Route::post('hospital/edit/{id}',[
    'as'=>'updateHospital',
    'uses'=> 'HospitalController@updateHospital'
]);

Route::get('hospital/delete/{id}',[
    'as'=>'deleteHospital',
    'uses'=> 'HospitalController@deleteHospital'
]);

Route::post('hospital/contact/delete/{id}',[
    'as'=>'deleteHospitalContact',
    'uses'=> 'HospitalController@deleteHospitalContact'
]);