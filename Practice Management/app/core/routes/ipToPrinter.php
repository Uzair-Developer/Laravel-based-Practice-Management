<?php

Route::get('ip-to-printer',[
    'as'=>'ipToPrinter',
    'uses'=> 'IpToPrinterController@ipToPrinter'
]);

Route::get('ip-to-printer/add',[
    'as'=>'addIpToPrinter',
    'uses'=> 'IpToPrinterController@addIpToPrinter'
]);

Route::post('ip-to-printer/add',[
    'as'=>'createIpToPrinter',
    'uses'=> 'IpToPrinterController@createIpToPrinter'
]);

Route::get('ip-to-printer/edit/{id}',[
    'as'=>'editIpToPrinter',
    'uses'=> 'IpToPrinterController@editIpToPrinter'
]);

Route::post('ip-to-printer/edit/{id}',[
    'as'=>'updateIpToPrinter',
    'uses'=> 'IpToPrinterController@updateIpToPrinter'
]);

Route::get('ip-to-printer/delete/{id}',[
    'as'=>'deleteIpToPrinter',
    'uses'=> 'IpToPrinterController@deleteIpToPrinter'
]);

Route::post('ip-to-printer/get-by-hospital-id',[
    'as'=>'getPrinterByHospitalId',
    'uses'=> 'IpToPrinterController@getPrinterByHospitalId'
]);

Route::post('ip-to-printer/get-printer-by-hospital',[
    'as'=>'getPrinterByHospital',
    'uses'=> 'IpToPrinterController@getPrinterByHospital'
]);

Route::post('ip-to-printer/get-default-printer',[
    'as'=>'getDefaultPrinter',
    'uses'=> 'IpToPrinterController@getDefaultPrinter'
]);

Route::post('ip-to-printer/set-default-printer',[
    'as'=>'setDefaultPrinter',
    'uses'=> 'IpToPrinterController@setDefaultPrinter'
]);
