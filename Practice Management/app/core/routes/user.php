<?php

Route::get('users',[
    'as'=>'users',
    'uses'=> 'UserController@index'
]);

Route::get('user/add',[
    'as'=>'addUser',
    'uses'=> 'UserController@addUser'
]);

Route::post('user/add',[
    'as'=>'createUser',
    'uses'=> 'UserController@createUser'
]);

Route::get('user/edit/{id}',[
    'as'=>'editUser',
    'uses'=> 'UserController@editUser'
]);

Route::post('user/edit/{id}',[
    'as'=>'updateUser',
    'uses'=> 'UserController@updateUser'
]);

Route::get('user/status/{id}',[
    'as'=>'changeStatus',
    'uses'=> 'UserController@changeStatus'
]);

Route::get('user/delete/{id}',[
    'as'=>'deleteUser',
    'uses'=> 'UserController@deleteUser'
]);

Route::get('login', [
    'as' => 'loginForm',
    'uses' => 'UserController@loginForm'
]);

Route::post('login', [
    'as' => 'login',
    'uses' => 'UserController@login'
]);

Route::get('logout', [
    'as' => 'logout',
    'uses' => 'UserController@logout'
]);

Route::post('change-password-first', [
    'as' => 'mustChangePassword',
    'uses' => 'UserController@mustChangePassword'
]);


Route::get('user/reset-password/{id}',[
    'as'=>'resetPassword',
    'uses'=> 'UserController@resetPassword'
]);

Route::post('user/change-password/{id}',[
    'as'=>'changePassword',
    'uses'=> 'UserController@changePassword'
]);

Route::get('user/profile/edit',[
    'as'=>'editProfile',
    'uses'=> 'UserController@editProfile'
]);

Route::post('user/physician-profile/edit',[
    'as'=>'updatePhysicianProfile',
    'uses'=> 'UserController@updatePhysicianProfile'
]);

Route::post('user/profile/edit',[
    'as'=>'updateProfile',
    'uses'=> 'UserController@updateProfile'
]);

Route::post('user/print-excel',[
    'as'=>'printExcelUsers',
    'uses'=> 'UserController@printExcelUsers'
]);

Route::get('user/permissions/{id}',[
    'as'=>'addSecurity',
    'uses'=> 'SecurityController@addSecurity'
]);

Route::post('user/permissions/{id}',[
    'as'=>'createSecurity',
    'uses'=> 'SecurityController@createSecurity'
]);


Route::get('groups',[
    'as'=>'listGroup',
    'uses'=> 'SecurityController@listGroup'
]);

Route::get('group/add',[
    'as'=>'addGroup',
    'uses'=> 'SecurityController@addGroup'
]);

Route::post('group/add',[
    'as'=>'createGroup',
    'uses'=> 'SecurityController@createGroup'
]);

Route::get('group/edit/{id}',[
    'as'=>'editGroup',
    'uses'=> 'SecurityController@editGroup'
]);

Route::post('group/edit/{id}',[
    'as'=>'updateGroup',
    'uses'=> 'SecurityController@updateGroup'
]);

Route::get('group/delete/{id}',[
    'as'=>'deleteGroup',
    'uses'=> 'SecurityController@deleteGroup'
]);
