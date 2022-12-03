<?php

Route::get('clinics', [
    'as' => 'dListClinic',
    'uses' => 'DClinicController@dListClinic'
]);

Route::get('clinic/add', [
    'as' => 'dAddClinic',
    'uses' => 'DClinicController@dAddClinic'
]);

Route::post('clinic/add', [
    'as' => 'dCreateClinic',
    'uses' => 'DClinicController@dCreateClinic'
]);

Route::get('clinic/edit/{id}', [
    'as' => 'dEditClinic',
    'uses' => 'DClinicController@dEditClinic'
]);

Route::post('clinic/edit/{id}', [
    'as' => 'dUpdateClinic',
    'uses' => 'DClinicController@dUpdateClinic'
]);

Route::get('clinic/delete/{id}', [
    'as' => 'dDeleteClinic',
    'uses' => 'DClinicController@dDeleteClinic'
]);
