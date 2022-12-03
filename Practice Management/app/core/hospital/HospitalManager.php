<?php
namespace core\hospital;


use core\BaseManager;
use core\enums\ResponseTypes;
use core\hospitalContact\HospitalContactRepository;
use Input;

class HospitalManager extends BaseManager
{
    function __construct()
    {
        $this->HospitalValidator = new HospitalValidator();
    }

    public function createHospital($inputs)
    {
        if (isset($inputs['contact'])){
            $contacts = $inputs['contact'];
        } else {
            $contacts = array();
        }
        unset($inputs['contact']);
        $validator = $this->HospitalValidator->validateHospital($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            if (!empty($inputs['logo'])) {
                $file = Input::file('logo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path() . '/uploads/hospitals';
                $upload_success = $file->move($destinationPath, $filename);
                if ($upload_success) {
                    $inputs['logo'] = 'uploads/hospitals/' . $filename;
                }
            } else {
                unset($inputs['logo']);
            }

            $Repo = new HospitalRepository();
            $hospital = $Repo->save($inputs);
            ///////////////////contacts//////////////////////
            $hospitalContactRepo = new HospitalContactRepository();
            foreach ($contacts as $key => $val) {
                $parts = explode('--', $val);
                $contactArray = array(
                    'hospital_id' => $hospital['id'],
                    'department_name' => $parts[0],
                    'phone' => $parts[1],
                    'extension' => $parts[2],
                    'show_in_sms' => $parts[3]
                );
                $hospitalContactRepo->save($contactArray);
            }
            return $this->response()->ResponseObject(ResponseTypes::success, 'Added Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Ops, Their is error please try again");
        }

    }

    public function updateHospital($inputs, $id)
    {
        if (isset($inputs['contact'])){
            $contacts = $inputs['contact'];
        } else {
            $contacts = array();
        }
        if (isset($inputs['newContact'])){
            $newContact = $inputs['newContact'];
        } else {
            $newContact = array();
        }
        unset($inputs['contact']);
        unset($inputs['newContact']);

        $validator = $this->HospitalValidator->validateHospital($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            if (!empty($inputs['logo'])) {
                $file = Input::file('logo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path() . '/uploads/hospitals';
                $upload_success = $file->move($destinationPath, $filename);
                if ($upload_success) {
                    $inputs['logo'] = 'uploads/hospitals/' . $filename;
                }
            } else {
                unset($inputs['logo']);
            }
            $Repo = new HospitalRepository();
            $Repo->update($inputs, $id);
            //////////////contacts/////////////////////
            $hospitalContactRepo = new HospitalContactRepository();
            foreach ($contacts as $key => $val) {
                $parts = explode('--', $val);
                $contactArray = array(
                    'department_name' => $parts[1],
                    'phone' => $parts[2],
                    'extension' => $parts[3],
                    'show_in_sms' => $parts[4]
                );
                $hospitalContactRepo->update($contactArray, $parts[0]);
            }
            foreach ($newContact as $key => $val) {
                $parts = explode('--', $val);
                $contactArray = array(
                    'hospital_id' => $id,
                    'department_name' => $parts[0],
                    'phone' => $parts[1],
                    'extension' => $parts[2],
                    'show_in_sms' => $parts[3]
                );
                $hospitalContactRepo->save($contactArray);
            }
            return $this->response()->ResponseObject(ResponseTypes::success, 'Updated Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Ops, Their is error please try again");
        }

    }


}