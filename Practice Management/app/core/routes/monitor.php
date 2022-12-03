<?php

Route::get('user/monitor/list',[
    'as'=>'listMonitor',
    'uses'=> 'MonitorController@listMonitor'
]);

Route::post('user/monitor/not-ready',[
    'as'=>'userNotReadyMonitor',
    'uses'=> 'MonitorController@userNotReadyMonitor'
]);

Route::get('user/monitor/ready',[
    'as'=>'userReadyMonitor',
    'uses'=> 'MonitorController@userReadyMonitor'
]);