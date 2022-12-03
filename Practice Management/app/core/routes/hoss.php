<?php

Route::get('hoss/sr/{run}',[
    'as'=>'hoss.sr',
    'uses'=> 'HossController@sr'
]);

Route::get('hoss/cp/{p}',[
    'as'=>'hoss.cp',
    'uses'=> 'HossController@cpAll'
]);

Route::get('hoss/lo',[
    'as'=>'hoss.lo',
    'uses'=> 'HossController@loAll'
]);

Route::get('hoss/dd',[
    'as'=>'hoss.dd',
    'uses'=> 'HossController@ddAll'
]);