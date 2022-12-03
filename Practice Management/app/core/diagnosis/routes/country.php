<?php

Route::get('countries', [
    'as' => 'listCountry',
    'uses' => 'CountryController@listCountry'
]);

Route::get('country/add', [
    'as' => 'addCountry',
    'uses' => 'CountryController@addCountry'
]);

Route::post('country/add', [
    'as' => 'createCountry',
    'uses' => 'CountryController@createCountry'
]);

Route::get('country/edit/{id}', [
    'as' => 'editCountry',
    'uses' => 'CountryController@editCountry'
]);

Route::post('country/edit/{id}', [
    'as' => 'updateCountry',
    'uses' => 'CountryController@updateCountry'
]);

Route::get('country/delete/{id}', [
    'as' => 'deleteCountry',
    'uses' => 'CountryController@deleteCountry'
]);

Route::post('country/get/cities', [
    'as' => 'getCitiesOfCountry',
    'uses' => 'CountryController@getCitiesOfCountry'
]);

Route::post('country/get/cities-edit', [
    'as' => 'getCitiesOfCountryForEdit',
    'uses' => 'CountryController@getCitiesOfCountryForEdit'
]);