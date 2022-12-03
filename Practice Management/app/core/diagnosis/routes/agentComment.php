<?php

Route::get('agentComments', [
    'as' => 'listAgentComment',
    'uses' => 'AgentCommentController@listAgentComment'
]);

Route::get('agentComments/add', [
    'as' => 'addAgentComment',
    'uses' => 'AgentCommentController@addAgentComment'
]);

Route::post('agentComments/add', [
    'as' => 'createAgentComment',
    'uses' => 'AgentCommentController@createAgentComment'
]);

Route::get('agentComments/delete/{id}', [
    'as' => 'deleteAgentComment',
    'uses' => 'AgentCommentController@deleteAgentComment'
]);

Route::get('agentComments/read/{id}', [
    'as' => 'readAgentComment',
    'uses' => 'AgentCommentController@readAgentComment'
]);
