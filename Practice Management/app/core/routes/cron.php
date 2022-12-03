<?php

Route::get('send-sms/all',[
    'as'=>'getAllSMS',
    'uses'=> 'CronController@getAllSMS'
]);

Route::get('portal-patient-send-sms/all',[
    'as'=>'getAllPortalPatientSMS',
    'uses'=> 'CronController@getAllPortalPatientSMS'
]);

Route::get('campaign-send-sms/all',[
    'as'=>'getAllCampaignSMS',
    'uses'=> 'CronController@getAllCampaignSMS'
]);

Route::get('reservation/close-all-clinics',[
    'as'=>'closeAllClinics',
    'uses'=> 'CronController@closeAllClinics'
]);

Route::get('save-sms-patient-lab-radiology',[
    'as'=>'saveSmsPatientLabRadiology',
    'uses'=> 'CronController@saveSmsPatientLabRadiology'
]);

Route::get('reservations-send-survey-url',[
    'as'=>'reservationsSendSurveyUrl',
    'uses'=> 'CronController@reservationsSendSurveyUrl'
]);

Route::get('in-patient-send-survey-url',[
    'as'=>'inPatientSendSurveyUrl',
    'uses'=> 'CronController@inPatientSendSurveyUrl'
]);

Route::get('save-sms-reminder-reservation',[
    'as'=>'saveSMSReminderReservation',
    'uses'=> 'CronController@saveSMSReminderReservation'
]);

Route::get('patients/send-new-patients-to-his',[
    'as'=>'sendNewPatientsToHIS',
    'uses'=> 'CronController@sendNewPatientsToHIS'
]);
