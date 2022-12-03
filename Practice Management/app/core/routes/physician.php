<?php

Route::get('physicians',[
    'as'=>'physicians',
    'uses'=> 'PhysicianController@index'
]);

Route::get('physician/edit/{id}',[
    'as'=>'editPhysician',
    'uses'=> 'PhysicianController@editPhysician'
]);

Route::post('physician/edit/{id}',[
    'as'=>'updatePhysician',
    'uses'=> 'PhysicianController@updatePhysician'
]);

Route::post('physician/get-physician-by-clinic',[
    'as'=>'getPhysicianByClinicId',
    'uses'=> 'PhysicianController@getPhysicianByClinicId'
]);

Route::post('physician/get-physician-by-clinics',[
    'as'=>'getPhysicianByClinicIds',
    'uses'=> 'PhysicianController@getPhysicianByClinicIds'
]);

Route::post('physician/get-any-clinic-and-hospital-by-clinic',[
    'as'=>'getAnyClinicAndHospitalByPhysician',
    'uses'=> 'PhysicianController@getAnyClinicAndHospitalByPhysician'
]);

Route::post('physician/get-profile',[
    'as'=>'getPhysicianProfile',
    'uses'=> 'PhysicianController@getPhysicianProfile'
]);

Route::get('physician/change-profile-status/{id}/{status}',[
    'as'=>'changeProfileStatus',
    'uses'=> 'PhysicianController@changeProfileStatus'
]);

Route::get('physician/his-import-physician',[
    'as'=>'hisImportPhysician',
    'uses'=> 'PhysicianController@hisImportPhysician'
]);

Route::post('physician/get-physicians-his',[
    'as'=>'getPhysiciansFromHIS',
    'uses'=> 'PhysicianController@getPhysiciansFromHIS'
]);

Route::post('physician/update-arabic-name-physicians',[
    'as'=>'updateArabicNamePhysicians',
    'uses'=> 'PhysicianController@updateArabicNamePhysicians'
]);

Route::post('physician/get-activate-physician-from-his-form',[
    'as'=>'getActivatePhysicianFromHISForm',
    'uses'=> 'PhysicianController@getActivatePhysicianFromHISForm'
]);

Route::post('physician/post-activate-physician-from-his-form',[
    'as'=>'postActivatePhysicianFromHISForm',
    'uses'=> 'PhysicianController@postActivatePhysicianFromHISForm'
]);

Route::post('physician/upload-image',[
    'as'=>'uploadPhysicianImage',
    'uses'=> 'PhysicianController@uploadPhysicianImage'
]);

Route::post('physician/save-tap-0',[
    'as'=>'saveTap0',
    'uses'=> 'PhysicianController@saveTap0'
]);

Route::post('physician/save-tap-1',[
    'as'=>'saveTap1',
    'uses'=> 'PhysicianController@saveTap1'
]);

Route::post('physician/save-tap-2',[
    'as'=>'saveTap2',
    'uses'=> 'PhysicianController@saveTap2'
]);
