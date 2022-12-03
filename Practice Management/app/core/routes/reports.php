<?php

Route::get('physician/get-reports',[
    'as'=>'getPhysicianReports',
    'uses'=> 'ReportsController@getPhysicianReports'
]);

Route::post('physician/get-reports',[
    'as'=>'postPhysicianReports',
    'uses'=> 'ReportsController@postPhysicianReports'
]);

Route::get('physician/print-excel-physician-report',[
    'as'=>'excelPhysicianReport',
    'uses'=> 'ReportsController@excelPhysicianReport'
]);

Route::get('clinic/get-reports',[
    'as'=>'getClinicReports',
    'uses'=> 'ReportsController@getClinicReports'
]);

Route::post('clinic/get-reports',[
    'as'=>'postClinicReports',
    'uses'=> 'ReportsController@postClinicReports'
]);

Route::get('clinic/print-excel-clinic-report',[
    'as'=>'excelClinicReport',
    'uses'=> 'ReportsController@excelClinicReport'
]);

Route::get('physician/physician-exception-report',[
    'as'=>'getPhysicianExceptionReports',
    'uses'=> 'ReportsController@getPhysicianExceptionReports'
]);

Route::post('physician/physician-exception-report',[
    'as'=>'postPhysicianExceptionReports',
    'uses'=> 'ReportsController@postPhysicianExceptionReports'
]);

Route::get('physician/print-excel-physician-exception-report',[
    'as'=>'excelPhysicianExceptionReport',
    'uses'=> 'ReportsController@excelPhysicianExceptionReport'
]);