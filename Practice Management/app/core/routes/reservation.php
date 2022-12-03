<?php

Route::get('reservation/add',[
    'as'=>'reservationAddMethodGet',
    'uses'=> 'ReservationController@reservationAddMethodGet'
]);

Route::post('reservation/add',[
    'as'=>'reservationAdd',
    'uses'=> 'ReservationController@reservationAdd'
]);

Route::post('reservation/get-physician-time',[
    'as'=>'getPhysicianTime',
    'uses'=> 'ReservationController@getPhysicianTime'
]);
Route::post('reservation/create-reservation',[
    'as'=>'createReservation',
    'uses'=> 'ReservationController@createReservation'
]);
Route::post('reservation/delete-reservation',[
    'as'=>'deleteReservation',
    'uses'=> 'ReservationController@deleteReservation'
]);
Route::post('reservation/add-note-reservation',[
    'as'=>'addNoteReservation',
    'uses'=> 'ReservationController@addNoteReservation'
]);
Route::get('reservation/reservation-get-events',[
    'as'=>'reservationGetEvents',
    'uses'=> 'ReservationController@reservationGetEvents'
]);
Route::post('reservation/get-reservations-of-patient',[
    'as'=>'searchPatientReservation',
    'uses'=> 'ReservationController@searchPatientReservation'
]);
Route::post('reservation/load-physician-time',[
    'as'=>'loadPhysicianTime',
    'uses'=> 'ReservationController@loadPhysicianTime'
]);
Route::post('reservation/get-available-physician-time',[
    'as'=>'getAvailablePhysicianTime',
    'uses'=> 'ReservationController@getAvailablePhysicianTime'
]);
Route::post('reservation/edit-reservation',[
    'as'=>'editReservation',
    'uses'=> 'ReservationController@editReservation'
]);
Route::post('reservation/update-reservation',[
    'as'=>'updateReservation',
    'uses'=> 'ReservationController@updateReservation'
]);

Route::post('reservation/get-data-by-id',[
    'as'=>'getReservationData',
    'uses'=> 'ReservationController@getReservationData'
]);

Route::post('reservation/get-parent-data-by-id',[
    'as'=>'getParentReservationData',
    'uses'=> 'ReservationController@getParentReservationData'
]);

Route::post('reservation/get-revisit-reservations-by-id',[
    'as'=>'getRevisitReservationData',
    'uses'=> 'ReservationController@getRevisitReservationData'
]);

Route::post('reservation/create-revisit-reservation',[
    'as'=>'createRevisitReservation',
    'uses'=> 'ReservationController@createRevisitReservation'
]);

Route::post('reservation/update-revisit-reservation',[
    'as'=>'updateRevisitReservation',
    'uses'=> 'ReservationController@updateRevisitReservation'
]);

Route::post('reservation/available-revisit-time',[
    'as'=>'getAvailableRevisitTime',
    'uses'=> 'ReservationController@getAvailableRevisitTime'
]);

Route::post('reservation/stand-alon-revisit-reservation',[
    'as'=>'standAlonRevisitReservation',
    'uses'=> 'ReservationController@standAlonRevisitReservation'
]);

Route::post('reservation/get-data',[
    'as'=>'getFirstFreeSlot',
    'uses'=> 'ReservationController@getFirstFreeSlot'
]);

Route::post('reservation/un-archive',[
    'as'=>'unArchiveReservation',
    'uses'=> 'ReservationController@unArchiveReservation'
]);

Route::post('reservation/resend-last-sms',[
    'as'=>'resendLastSms',
    'uses'=> 'ReservationController@resendLastSms'
]);
