<?php

Route::get('physician-calendar',[
    'as'=>'viewCalendar',
    'uses'=> 'PhysicianCalendarController@viewCalendar'
]);

Route::get('physician-calendar/events',[
    'as'=>'physicianCalendarGetEvents',
    'uses'=> 'PhysicianCalendarController@physicianCalendarGetEvents'
]);

Route::post('physician-calendar/add-physician-exception',[
    'as'=>'addPhysicianExceptionPopUp',
    'uses'=> 'PhysicianCalendarController@addPhysicianExceptionPopUp'
]);
