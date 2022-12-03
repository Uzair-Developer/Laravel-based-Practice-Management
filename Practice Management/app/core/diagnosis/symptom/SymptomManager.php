<?php
namespace core\diagnosis\symptom;


use core\BaseManager;
use core\enums\ResponseTypes;

class SymptomManager extends BaseManager
{
    function __construct()
    {
        $this->SymptomValidator = new SymptomValidator();
    }

    public function addSymptom($inputs)
    {
        $validator = $this->SymptomValidator->symptomValidate($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $symptomRepo = new SymptomRepository();
            $symptomRepo->save($inputs);
            return $this->response()->ResponseObject(ResponseTypes::success, 'Added Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Error Occurred");
        }

    }


    public function updateSymptom($inputs, $id)
    {

        $validator = $this->SymptomValidator->symptomValidate($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $symptomRepo = new SymptomRepository();
            $symptomRepo->update($inputs, $id);
            return $this->response()->ResponseObject(ResponseTypes::success, 'Updated Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Error Occurred");
        }

    }

    public function delete($id)
    {
        $ORepo = new SymptomRepository();
        $ORepo->delete($id);
        return $this->response()->ResponseObject(ResponseTypes::success, 'Deleted Successfully');
    }


}