<?php
namespace core\clinic;


use core\BaseManager;
use core\enums\ResponseTypes;
use Input;

class ClinicManager extends BaseManager
{
    function __construct()
    {
        $this->ClinicValidator = new ClinicValidator();
    }

    public function createClinic($inputs)
    {

        $validator = $this->ClinicValidator->validateClinic($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $Repo = new ClinicRepository();
            $Repo->save($inputs);
            return $this->response()->ResponseObject(ResponseTypes::success, 'Added Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Ops, Their is error please try again");
        }

    }

    public function updateClinic($inputs, $id)
    {

        $validator = $this->ClinicValidator->validateClinic($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $Repo = new ClinicRepository();
            $Repo->update($inputs, $id);
            return $this->response()->ResponseObject(ResponseTypes::success, 'Updated Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Ops, Their is error please try again");
        }

    }


}