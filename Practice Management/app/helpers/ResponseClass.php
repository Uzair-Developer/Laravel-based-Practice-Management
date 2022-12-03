<?php
use Laracasts\Flash\Flash;
use core\enums\ResponseTypes;

class ResponseClass
{

    public function ResponseObject($status, $msg, $return = null, $setFlash = true)
    {
        $ResponseArray = array();
        $ResponseArray['status'] = $status;
        $ResponseArray['msg'] = $msg;
        $ResponseArray['return'] = $return;
        if ($setFlash) {
            $this->setFlashMessage($status, $msg);
        }
        return $ResponseArray;
    }

    public function setFlashMessage($status, $msg)
    {
        if (!empty($msg)) {
            if ($status == ResponseTypes::error)
                Flash::error($msg);
            elseif ($status == ResponseTypes::success)
                Flash::success($msg);
        }
    }
}