<?php

Route::get('stand-alone-revisit',[
    'as'=>'standAloneRevisit',
    'uses'=> 'StandAloneRevisitController@standAloneRevisit'
]);