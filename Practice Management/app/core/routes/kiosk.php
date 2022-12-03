<?php

Route::get('kiosk/step1', [
    'as' => 'kioskStep1',
    'uses' => 'KioskController@kioskStep1'
]);

Route::post('kiosk/back-to-step1', [
    'as' => 'kioskBack',
    'uses' => 'KioskController@kioskBack'
]);

Route::post('kiosk/no-reservation', [
    'as' => 'kioskNoReservation',
    'uses' => 'KioskController@kioskNoReservation'
]);

Route::post('kiosk/with-reservation', [
    'as' => 'kioskGetWithReservation',
    'uses' => 'KioskController@kioskGetWithReservation'
]);

Route::post('kiosk/get-with-reservation-code', [
    'as' => 'kioskGetWithReservationCode',
    'uses' => 'KioskController@kioskGetWithReservationCode'
]);

Route::post('kiosk/with-reservation-print', [
    'as' => 'kioskWithReservationPrint',
    'uses' => 'KioskController@kioskWithReservationPrint'
]);

Route::post('kiosk/with-reservation-convert-to-waiting', [
    'as' => 'kioskWithReservationConvertToWaiting',
    'uses' => 'KioskController@kioskWithReservationConvertToWaiting'
]);

Route::get('kiosk/pharmacy', [
    'as' => 'kioskPharmacy',
    'uses' => 'KioskController@kioskPharmacy'
]);

Route::post('kiosk/pharmacy/print-ticket', [
    'as' => 'kioskPrintPharmacyTicket',
    'uses' => 'KioskController@kioskPrintPharmacyTicket'
]);