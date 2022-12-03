<?php
namespace core\diagnosis\organ;


use core\BaseManager;
use core\enums\ResponseTypes;

class OrganManager extends BaseManager
{
    function __construct()
    {
        $this->OrganValidator = new OrganValidator();
    }

    public function addOrgan($inputs)
    {
        $validator = $this->OrganValidator->organValidate($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $organRepo = new OrganRepository();
            $organRepo->save($inputs);
            return $this->response()->ResponseObject(ResponseTypes::success, 'Added Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Error Occurred");
        }

    }


    public function updateOrgan($inputs, $id)
    {

        $validator = $this->OrganValidator->organValidate($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $organRepo = new OrganRepository();
            $organRepo->update($inputs, $id);
            return $this->response()->ResponseObject(ResponseTypes::success, 'Updated Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Error Occurred");
        }

    }

    public function delete($id)
    {
        $ORepo = new OrganRepository();
        $ORepo->delete($id);
        return $this->response()->ResponseObject(ResponseTypes::success, 'Deleted Successfully');
    }


}