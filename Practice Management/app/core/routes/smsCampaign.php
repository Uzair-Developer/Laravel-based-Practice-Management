<?php

Route::get('sms-campaigns',[
    'as'=>'smsCampaign',
    'uses'=> 'SmsCampaignController@smsCampaign'
]);

Route::post('sms-campaign/download-template',[
    'as'=>'smsCampaignDownloadTemplate',
    'uses'=> 'SmsCampaignController@smsCampaignDownloadTemplate'
]);

Route::post('sms-campaign/send-new-group',[
    'as'=>'smsCampaignSendNewGroup',
    'uses'=> 'SmsCampaignController@smsCampaignSendNewGroup'
]);

Route::post('sms-campaign/send-exist-group',[
    'as'=>'smsCampaignSendExistGroup',
    'uses'=> 'SmsCampaignController@smsCampaignSendExistGroup'
]);