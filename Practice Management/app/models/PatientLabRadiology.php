<?php

class PatientLabRadiology extends Eloquent
{

    protected $table = 'patient_lab_radiology';
    protected $guarded = array('');


    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs, $all = false)
    {
        $data = self::where(function ($q) use ($inputs) {
            if (isset($inputs['date_from']) && $inputs['date_from']) {
                $q->where('datetime', '>=', $inputs['date_from'] . ' 00:00:00');
            }
            if (isset($inputs['date_to']) && $inputs['date_to']) {
                $q->where('datetime', '<=', $inputs['date_to'] . ' 23:59:59');
            }
            if (isset($inputs['hospital_id']) && $inputs['hospital_id'] && empty($inputs['clinic_id'])) {
                $physicianArray = UsersLocalizationClinics::getActivePhysiciansByHospitalId($inputs['hospital_id']);
                $q->whereIn('physician_id', $physicianArray);
            }
            if (isset($inputs['clinic_id']) && $inputs['clinic_id'] && empty($inputs['physician_id'])) {
                $physicianArray = UsersLocalizationClinics::getActivePhysiciansByClinicId($inputs['clinic_id']);
                $q->whereIn('physician_id', $physicianArray);
            }
            if (isset($inputs['physician_id']) && $inputs['physician_id']) {
                $q->where('physician_id', $inputs['physician_id']);
            }
            if (isset($inputs['patient_id']) && $inputs['patient_id']) {
                $q->where('patient_reg_no', $inputs['patient_id']);
            }
            if (isset($inputs['patient_name']) && $inputs['patient_name']) {
                $patientIds = Patient::searchPatient(array('name' => $inputs['patient_name']));
                $q->whereIn('patient_id', $patientIds);
            }
            if (isset($inputs['patient_phone']) && $inputs['patient_phone']) {
                $patientIds = Patient::searchPatient(array('phone' => $inputs['patient_phone']));
                $q->whereIn('patient_id', $patientIds);
            }
            if (isset($inputs['finished']) && $inputs['finished']) {
                if ($inputs['finished'] == 2) {
                    $q->whereNull('verifieddatetime');
                } elseif ($inputs['finished'] == 1) {
                    $q->whereNotNull('verifieddatetime');
                }
            }
            if (isset($inputs['type']) && $inputs['type']) {
                $q->where('station', $inputs['type']);

            }
        });
        if (isset($inputs['paginate']) && $inputs['paginate']) {
         $data = $data->paginate(200);
        } else {
            $data = $data->get();
        }
        $orderArray = array();
        if (isset($inputs['withoutDetails']) && $inputs['withoutDetails']) {
            foreach ($data as $key => $val) {
                if (!in_array($val['order_id'], $orderArray)) {
                    $orderArray[] = $val['order_id'];
                } else {
                    unset($data[$key]);
                }
            }
            return $data;
        }
        $orderArray = array();
        foreach ($data as $key => $val) {
            $patient = Patient::getById($val['patient_id']);
            $data[$key]['patient_lab_sms'] = $patient['lab_sms'];
            $data[$key]['patient_name'] = $patient['name'];
            $data[$key]['patient_phone'] = $patient['phone'];
            if (app('production')) {
                $data[$key]['password'] = HisUserProfile::getInitialPasswordByPatient($val['patient_reg_no']);
                $data[$key]['message_status'] = PortalPatientSMS::checkPatient_Date_Order($val['patient_id'], null, $val['order_id'])['response'];
            }
            if (!$all) {
                if (!in_array($val['order_id'], $orderArray)) {
                    $orderArray[] = $val['order_id'];
                } else {
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }

    public static function getOrderIds()
    {
        $date = date('Y-m-d', strtotime("-2 days"));
        return self::where('datetime', '>=', $date . ' 00:00:00.000')->groupBy('order_id')
            ->lists('order_id');
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getByOrder($order_id)
    {
        return self::where('order_id', $order_id)->get()->toArray();
    }

    public static function editByOrder_PatientReg_TestId($order_id, $patient_reg, $test_id, $inputs)
    {
        return self::where('order_id', $order_id)
            ->where('patient_reg_no', $patient_reg)
            ->where('test_id', $test_id)->update($inputs);
    }
}
