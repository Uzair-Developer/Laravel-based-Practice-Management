<?php

use core\enums\MainType;
use core\enums\UserRules;
use Laracasts\Flash\Flash;

class MonitorController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listMonitor()
    {
        if (!$this->user->hasAccess('monitor.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['monitor'] = User::getByRule(UserRules::physician);
        return View::make('monitor/list', $data);
    }

    public function userNotReadyMonitor()
    {
        $inputs = Input::except('_token');
        Monitor::add(array(
            'user_id' => $this->user->id,
            'status' => 0,
            'not_ready_reason_id' => $inputs['not_ready_reason_id'],
            'date' => date('Y-m-d'),
            'time' => date('H:i:s')
        ));
        User::edit(array(
            'is_ready' => 0,
        ), $this->user->id);
//        Flash::success('Updated Successfully');
        return Redirect::back();
    }

    public function userReadyMonitor()
    {
        Monitor::add(array(
            'user_id' => $this->user->id,
            'status' => 1,
            'date' => date('Y-m-d'),
            'time' => date('H:i:s')
        ));
        User::edit(array(
            'is_ready' => 1,
        ), $this->user->id);
//        Flash::success('Updated Successfully');
        return Redirect::back();
    }

}