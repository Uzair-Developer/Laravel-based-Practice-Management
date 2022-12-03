<?php
namespace core\publicHoliday;


use Clinic;
use core\BaseManager;
use core\enums\ResponseTypes;
use PublicHoliday;
use Reservation;

class PublicHolidayManager extends BaseManager
{
    function __construct()
    {
        $this->PublicHolidayValidator = new PublicHolidayValidator();
    }

    public function createPublicHoliday($inputs)
    {

        $validator = $this->PublicHolidayValidator->validatePublicHoliday($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $Repo = new PublicHolidayRepository();
            $Repo->save($inputs);
            $clinic_ids = Clinic::getByHospitalId($inputs['hospital_id']);
            Reservation::pendingWithPeriod($clinic_ids, $inputs['from_date'], $inputs['to_date'], '', '', $inputs['name']);
            return $this->response()->ResponseObject(ResponseTypes::success, 'Added Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Ops, Their is error please try again");
        }

    }

    public function updatePublicHoliday($inputs, $id)
    {

        $validator = $this->PublicHolidayValidator->validatePublicHoliday($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $Repo = new PublicHolidayRepository();
            $public = PublicHoliday::getById($id);
            $Repo->update($inputs, $id);
            if ($public['from_date'] != $inputs['from_date'] || $public['from_to'] != $inputs['from_to']) {
                $clinic_ids = Clinic::getByHospitalId($inputs['hospital_id']);
                Reservation::reservedWithPeriod($clinic_ids, $inputs['from_date'], $inputs['to_date']);
                Reservation::pendingWithPeriod($clinic_ids, $inputs['from_date'], $inputs['to_date']);
            }
            return $this->response()->ResponseObject(ResponseTypes::success, 'Updated Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Ops, Their is error please try again");
        }

    }


}