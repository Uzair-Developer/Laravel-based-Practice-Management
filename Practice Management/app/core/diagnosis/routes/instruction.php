<?php

Route::get('instructions/edit/{id}', [
    'as' => 'editInstruction',
    'uses' => 'InstructionController@editInstruction'
]);

Route::post('instructions/edit/{id}', [
    'as' => 'updateInstruction',
    'uses' => 'InstructionController@updateInstruction'
]);
