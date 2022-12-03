<?php

Route::get('kiosk-to-printer',[
    'as'=>'kioskToPrinter',
    'uses'=> 'KioskToPrinterController@kioskToPrinter'
]);

Route::get('kiosk-to-printer/add',[
    'as'=>'addKioskToPrinter',
    'uses'=> 'KioskToPrinterController@addKioskToPrinter'
]);

Route::post('kiosk-to-printer/add',[
    'as'=>'createKioskToPrinter',
    'uses'=> 'KioskToPrinterController@createKioskToPrinter'
]);

Route::get('kiosk-to-printer/edit/{id}',[
    'as'=>'editKioskToPrinter',
    'uses'=> 'KioskToPrinterController@editKioskToPrinter'
]);

Route::post('kiosk-to-printer/edit/{id}',[
    'as'=>'updateKioskToPrinter',
    'uses'=> 'KioskToPrinterController@updateKioskToPrinter'
]);

Route::get('kiosk-to-printer/delete/{id}',[
    'as'=>'deleteKioskToPrinter',
    'uses'=> 'KioskToPrinterController@deleteKioskToPrinter'
]);

Route::post('kiosk-to-printer/get-by-hospital-id',[
    'as'=>'getKioskByHospitalId',
    'uses'=> 'KioskToPrinterController@getKioskByHospitalId'
]);