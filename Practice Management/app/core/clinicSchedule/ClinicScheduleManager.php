<?php
namespace core\clinicSchedule;


use ClinicSchedule;
use core\BaseManager;
use core\enums\ResponseTypes;
use Input;

class ClinicScheduleManager extends BaseManager
{
    function __construct()
    {
        $this->ClinicScheduleValidator = new ClinicScheduleValidator();
    }

    public function createClinicSchedule($inputs)
    {
        $validator = $this->ClinicScheduleValidator->validateClinicSchedule($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $inputs['create_timestamp'] = time();
            $inputs['shift1_day_of'] = !empty($inputs['shift1_day_of']) ? implode(',', $inputs['shift1_day_of']) : '';
            $inputs['shift2_day_of'] = !empty($inputs['shift2_day_of']) ? implode(',', $inputs['shift2_day_of']) : '';
            $inputs['shift3_day_of'] = !empty($inputs['shift3_day_of']) ? implode(',', $inputs['shift3_day_of']) : '';
            if (!empty($inputs['start_date']) && !empty($inputs['end_date'])) {
                if ($inputs['start_date'] > $inputs['end_date']) {
                    return $this->response()->ResponseObject(ResponseTypes::error, "Make sure the end date is greater than start date");
                }
            }
            $Repo = new ClinicScheduleRepository();
            if (!empty($inputs['start_date'])) {
                if ($Repo->checkDataIsAvailable($inputs['start_date'], null, $inputs['clinic_id'])) {
                    return $this->response()->ResponseObject(ResponseTypes::error, "Start date is used in another schedule");
                }
                $lastSchedule = ClinicSchedule::getAllByClinicId($inputs['clinic_id'])->last();
                if ($lastSchedule) {
                    $start_date = date("Y-m-d", strtotime("+1 day", strtotime($lastSchedule['end_date'])));
                    if ($start_date != $inputs['start_date']) {
                        return $this->response()->ResponseObject(ResponseTypes::error, "We found blank days from end date of last schedule: " . $lastSchedule['end_date']);
                    }
                }
            }
            if (!empty($inputs['end_date'])) {
                if ($Repo->checkDataIsAvailable($inputs['end_date'], null, $inputs['clinic_id'])) {
                    return $this->response()->ResponseObject(ResponseTypes::error, "End date is used in another schedule");
                }
            }
            $inputs['name'] = $inputs['start_date'] . ' ' . $inputs['end_date'];
            if ($inputs['num_of_shifts'] == 1) {
                $inputs['shift2_start_time'] = null;
                $inputs['shift2_end_time'] = null;
                $inputs['shift2_day_of'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                $inputs['shift3_start_time'] = null;
                $inputs['shift3_end_time'] = null;
                $inputs['shift3_day_of'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
            }
            if ($inputs['num_of_shifts'] == 2) {
                $inputs['shift3_start_time'] = null;
                $inputs['shift3_end_time'] = null;
                $inputs['shift3_day_of'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
            }
            $Repo->save($inputs);
            return $this->response()->ResponseObject(ResponseTypes::success, 'Added Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Ops, Their is error please try again");
        }
    }

    public function updateClinicSchedule($inputs, $id)
    {

        $validator = $this->ClinicScheduleValidator->validateClinicSchedule($inputs, true);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $inputs['shift1_day_of'] = !empty($inputs['shift1_day_of']) ? implode(',', $inputs['shift1_day_of']) : '';
            $inputs['shift2_day_of'] = !empty($inputs['shift2_day_of']) ? implode(',', $inputs['shift2_day_of']) : '';
            $inputs['shift3_day_of'] = !empty($inputs['shift3_day_of']) ? implode(',', $inputs['shift3_day_of']) : '';
            if (!empty($inputs['start_date']) && !empty($inputs['end_date'])) {
                if ($inputs['start_date'] > $inputs['end_date']) {
                    return $this->response()->ResponseObject(ResponseTypes::error, "Make sure the End date is greater than start date");
                }
            }
            $Repo = new ClinicScheduleRepository();
            if (!empty($inputs['start_date'])) {
                if ($Repo->checkDataIsAvailable($inputs['start_date'], $id, $inputs['clinic_id'])) {
                    return $this->response()->ResponseObject(ResponseTypes::error, "Start date is used in another schedule");
                }
            }
            if (!empty($inputs['end_date'])) {
                if ($Repo->checkDataIsAvailable($inputs['end_date'], $id, $inputs['clinic_id'])) {
                    return $this->response()->ResponseObject(ResponseTypes::error, "End date is used in another schedule");
                }
            }
            if ($inputs['num_of_shifts'] == 1) {
                $inputs['shift2_start_time'] = null;
                $inputs['shift2_end_time'] = null;
                $inputs['shift2_day_of'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                $inputs['shift3_start_time'] = null;
                $inputs['shift3_end_time'] = null;
                $inputs['shift3_day_of'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
            }
            if ($inputs['num_of_shifts'] == 2) {
                $inputs['shift3_start_time'] = null;
                $inputs['shift3_end_time'] = null;
                $inputs['shift3_day_of'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
            }
            $Repo->update($inputs, $id);
            return $this->response()->ResponseObject(ResponseTypes::success, 'Updated Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Ops, Their is error please try again");
        }

    }


}