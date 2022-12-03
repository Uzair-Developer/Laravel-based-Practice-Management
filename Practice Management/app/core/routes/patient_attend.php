<?php

Route::get('patient-attend/list', [
    'as' => 'listPatientAttend',
    'uses' => 'PatientAttendController@listPatientAttend'
]);