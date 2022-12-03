<?php
namespace core\diagnosis\clinic;


use core\BaseManager;
use core\enums\ResponseTypes;

class ClinicManager extends BaseManager
{
    function __construct()
    {
        $this->ClinicValidator = new ClinicValidator();
    }

    public function addClinic($inputs)
    {
        $validator = $this->ClinicValidator->clinicValidate($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $clinicRepo = new ClinicRepository();
            $clinicRepo->save($inputs);
            return $this->response()->ResponseObject(ResponseTypes::success, 'Added Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Error Occurred");
        }

    }


    public function updateClinic($inputs, $id)
    {

        $validator = $this->ClinicValidator->clinicValidate($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $clinicRepo = new ClinicRepository();
            $clinicRepo->update($inputs, $id);
            return $this->response()->ResponseObject(ResponseTypes::success, 'Updated Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Error Occurred");
        }

    }

    public function delete($id)
    {
        $ORepo = new ClinicRepository();
        $ORepo->delete($id);
        return $this->response()->ResponseObject(ResponseTypes::success, 'Deleted Successfully');
    }


}