<?php
namespace core;
use core\user\UserManipulator;
use ResponseClass;

class BaseManager
{
    protected $response = false;
    protected $currentUser = false;

    protected function response()
    {
        if (!$this->response) {
            $this->response = new ResponseClass();
        }
        return $this->response;
    }
}