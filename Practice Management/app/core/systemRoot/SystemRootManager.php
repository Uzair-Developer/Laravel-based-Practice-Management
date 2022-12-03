<?php
namespace core\systemRoot;


use core\BaseManager;
use core\enums\ResponseTypes;
use Input;

class SystemRootManager extends BaseManager
{
    function __construct()
    {
        $this->SystemRootValidator = new SystemRootValidator();
    }

    public function updateSystemRoot($inputs)
    {

        $validator = $this->SystemRootValidator->systemRootValidate($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            if (!empty($inputs['logo'])) {

                $file = Input::file('logo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path().'/uploads/systemRoot';
                $upload_success = $file->move($destinationPath, $filename);
                if ($upload_success) {
                    $inputs['logo'] = 'uploads/systemRoot/' . $filename;
                }
            } else {
                unset($inputs['logo']);
            }
            $systemRepo = new SystemRootRepository();
            $systemRepo->update($inputs, 1);


            return $this->response()->ResponseObject(ResponseTypes::success, 'Updated Successfully');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return $this->response()->ResponseObject(ResponseTypes::error, "Ops, Their is error please try again");
        }

    }


}