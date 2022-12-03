<?php

class InPatient extends Eloquent
{

    protected $table = 'in_patient';
    protected $guarded = array('');

    public static function add($inputs)
    {
        $inputs['create_timestamp'] = time();
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs = '')
    {
        $data = self::whereRaw('1 = 1');
        if (isset($inputs['from_admitdatetime']) && $inputs['from_admitdatetime']) {
            $data = $data->where('admitdatetime', '>=', $inputs['from_admitdatetime']);
        }

        if (isset($inputs['getIds']) && $inputs['getIds']) {
            $data = $data->lists($inputs['getIds']);
        } else {
            $data = $data->get();
        }
        return $data;
    }


    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function checkExist($patient_id, $ipid)
    {
        return self::where('patient_id', $patient_id)
            ->where('ipid', $ipid)
            ->first();
    }

    public static function getByPatient($patient_id)
    {
        return self::where('patient_id', $patient_id)
            ->first();
    }

    public static function getByRegistrationNo($patient_id)
    {
        return self::where('registration_no', $patient_id)
            ->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function sendSmsToAll()
    {
        self::chunk(25, function ($patients) {
            foreach ($patients as $key => $val) {
                $patient = Patient::getById($val['patient_id']);
                if ($patient) {
                    $lastRes = Reservation::getLastOfPatient($patient['id']);
                    if ($lastRes) {
                        if ($lastRes['sms_lang'] == 1) { // arabic
                            $body = trans('sms.in_patient_survey-ar');
                        } else { // english
                            $body = trans('sms.in_patient_survey');
                        }
                    } else {
                        $body = trans('sms.in_patient_survey-ar');
                    }
                    $smsArray = array(
                        'patient_id' => $patient['id'],
                        'reservation_id' => null,
                        'type' => 'in_patient_Survey',
                        'message' => $body,
                    );
                    PatientSMS::add($smsArray);
                }
            }
        });
    }
}
