<?php

Route::get('reservation/manage',[
    'as'=>'reservationManage',
    'uses'=> 'ReservationManagementController@reservationManage'
]);

Route::get('reservation/manage-clinic/{clinic_id}',[
    'as'=>'manageClinic',
    'uses'=> 'ReservationManagementController@manageClinic'
]);

Route::get('reservation/clinic',[
    'as'=>'manageClinicReservations',
    'uses'=> 'ReservationManagementController@manageClinicReservations'
]);

Route::get('reservation/clinic/manage-patient/{reservation_id}/{status}',[
    'as'=>'managePatientReservation',
    'uses'=> 'ReservationManagementController@managePatientReservation'
]);

Route::get('reservation/clinic/change-status/{reservation_id}/{status}',[
    'as'=>'changeStatusPatientReservation',
    'uses'=> 'ReservationManagementController@changeStatusPatientReservation'
]);

Route::post('reservation/clinic/print-excel',[
    'as'=>'printExcelManageClinicReservations',
    'uses'=> 'ReservationManagementController@printExcelManageClinicReservations'
]);

Route::post('reservation/walk-in/add',[
    'as'=>'addWalkInReservation',
    'uses'=> 'ReservationManagementController@addWalkInReservation'
]);

Route::get('reservation/patient-attend/{id}',[
    'as'=>'managePatientAttendReservation',
    'uses'=> 'ReservationManagementController@managePatientAttendReservation'
]);

Route::get('reservation/approved-walk-in/{id}',[
    'as'=>'approvedWalkInReservation',
    'uses'=> 'ReservationManagementController@approvedWalkInReservation'
]);

Route::get('reservation/next-patient',[
    'as'=>'nextPatientInReservation',
    'uses'=> 'ReservationManagementController@nextPatientInReservation'
]);

Route::post('reservation/get-total-count-refresh',[
    'as'=>'getReservationTotalCountRefresh',
    'uses'=> 'ReservationManagementController@getReservationTotalCountRefresh'
]);

Route::get('reservation/print-reservation',[
    'as'=>'PrintReservation',
    'uses'=> 'ReservationManagementController@PrintReservation'
]);

Route::get('reservation/history',[
    'as'=>'reservationHistory',
    'uses'=> 'ReservationManagementController@reservationHistory'
]);

Route::post('reservation/view-history',[
    'as'=>'reservationViewHistory',
    'uses'=> 'ReservationManagementController@reservationViewHistory'
]);
