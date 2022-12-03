<?php


use Cartalyst\Sentry\Facades\Laravel\Sentry;

Route::get('/',[
    'as'=>'home',
    'uses'=> 'HomeController@index'
]);

Route::post('get-reservation-counts',[
    'as'=>'getReservationCounts',
    'uses'=> 'HomeController@getReservationCounts'
]);

Route::get('testtt', function(){
//    $group = Sentry::createGroup(array(
//        'name'        => 'Supper Admin',
//        'permissions' => array(
//            'Supper Admin' => 1
//        ),
//    ));
//    $group = Sentry::createGroup(array(
//        'name'        => 'Hospital Admin',
//        'permissions' => array(
//            'Hospital Admin' => 1
//        ),
//    ));
//    $group = Sentry::createGroup(array(
//        'name'        => 'Clinic Manager',
//        'permissions' => array(
//            'Clinic Manager' => 1
//        ),
//    ));
//    $group = Sentry::createGroup(array(
//        'name'        => 'Call Center Agent',
//        'permissions' => array(
//            'Call Center Agent' => 1
//        ),
//    ));
//    $group = Sentry::createGroup(array(
//        'name'        => 'Visits Coordinator',
//        'permissions' => array(
//            'Visits Coordinator' => 1
//        ),
//    ));
//    $group = Sentry::createGroup(array(
//        'name'        => 'Reception Personnel',
//        'permissions' => array(
//            'Reception Personnel' => 1
//        ),
//    ));
//    $group = Sentry::createGroup(array(
//        'name'        => 'Physician',
//        'permissions' => array(
//            'Physician' => 1
//        ),
//    ));
});