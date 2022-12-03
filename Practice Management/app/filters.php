<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\authorized\Authorized;

Route::filter('csrf', function () {
    if (Session::token() != Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException;
    }
});

Route::filter('login', function () {
    if (!Sentry::check()) {
        return Redirect::route('loginForm');
    }
});

Route::filter('isAdmin', function () {
    $user = Sentry::getUser();
    if (!empty($user)) {
        if (!Authorized::isAdmin($user)) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
    } else {
        Flash::error('You don\'t have a permission to do this action');
        return Redirect::back();
    }
});

App::before(function ($request) {
    App::singleton('production', function () {
        return true;
    });
});
App::before(function ($request) {
    App::singleton('send_sms', function () {
        return true;
    });
});
App::before(function ($request) {
    App::singleton('send_sms_reminder', function () {
        return true;
    });
});

App::before(function ($request) {
    App::singleton('portal_send_sms', function () {
        return false;
    });
});
