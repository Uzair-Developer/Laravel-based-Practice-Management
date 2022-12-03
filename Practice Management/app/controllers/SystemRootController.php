<?php

use core\systemRoot\SystemRootManager;
use core\systemRoot\SystemRootRepository;

class SystemRootController extends BaseController
{

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
    }

    public function systemRoot()
    {
        $systemRepo = new SystemRootRepository();
        $data['data'] = $systemRepo->getAll();
        return View::make('systemRoot/edit', $data);
    }

    public function updateSystemRoot()
    {
        $systemManager = new SystemRootManager();
        $inputs = (Input::except('_token'));
        $systemManager->updateSystemRoot($inputs);
        return Redirect::back();
    }

}
