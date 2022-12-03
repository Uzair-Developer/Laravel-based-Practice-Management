<?php

Route::get('unlock-slots', [
    'as' => 'listUnlockSlot',
    'uses' => 'UnlockSlotController@listUnlockSlot'
]);

Route::get('unlock-slots/add', [
    'as' => 'addUnlockSlot',
    'uses' => 'UnlockSlotController@addUnlockSlot'
]);

Route::post('unlock-slots/add', [
    'as' => 'createUnlockSlot',
    'uses' => 'UnlockSlotController@createUnlockSlot'
]);

Route::get('unlock-slots/edit/{id}', [
    'as' => 'editUnlockSlot',
    'uses' => 'UnlockSlotController@editUnlockSlot'
]);

Route::post('unlock-slots/edit/{id}', [
    'as' => 'updateUnlockSlot',
    'uses' => 'UnlockSlotController@updateUnlockSlot'
]);

Route::get('unlock-slots/delete/{id}', [
    'as' => 'deleteUnlockSlot',
    'uses' => 'UnlockSlotController@deleteUnlockSlot'
]);

Route::post('unlock-slots/unlock', [
    'as' => 'unLockSlotReservation',
    'uses' => 'UnlockSlotController@unLockSlotReservation'
]);

Route::post('unlock-slots/lock-slot', [
    'as' => 'lockSlotReservation',
    'uses' => 'UnlockSlotController@lockSlotReservation'
]);