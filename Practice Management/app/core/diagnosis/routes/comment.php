<?php

Route::get('comments/{type}', [
    'as' => 'listComment',
    'uses' => 'CommentController@listComment'
]);

Route::get('comments/status/{id}/{type}', [
    'as' => 'statusComment',
    'uses' => 'CommentController@statusComment'
]);