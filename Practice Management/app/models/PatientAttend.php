<?php

class PatientAttend extends Eloquent
{
    protected $table = 'patient_attend';
    protected $guarded = array('');

    public static $rules = array();

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs = '')
    {
        $data = self::where(function ($query) use ($inputs) {
            if ($inputs) {
                if ($inputs['reservation_code']) {
                    $reservationIds = Reservation::getIdsByCode($inputs['reservation_code']);
                    $query->whereIn('reservation_id', $reservationIds);
                }
                if ($inputs['date']) {
                    $query->where('date', '>=', $inputs['date']);
                }
            } else {
                $query->where('date', date('Y-m-d'));
            }
        });
        if (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
            $data = $data->join('patients', 'patients.id', '=', 'patient_attend.patient_id')
                ->join('hospitals', 'hospitals.id', '=', 'patients.hospital_id')
                ->where('hospitals.id', $inputs['hospital_id']);
        }
        if (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
            $data = $data->paginate(25);
        } else {
            $data = $data->get()->toArray();
        }
        foreach ($data as $key => $val) {
            $data[$key]['created_by'] = User::getName($val['created_by']);
            $data[$key]['patient_name'] = Patient::getName($val['patient_id']);
            $data[$key]['patient_phone'] = Patient::getPhone($val['patient_id']);

            $reservationData = Reservation::getById($val['reservation_id'], false);
            $data[$key]['clinic_name'] = Clinic::getNameById($reservationData['clinic_id']);
            $data[$key]['reservation_code'] = $reservationData['code'];
            $data[$key]['physician_name'] = User::getNameById($reservationData['physician_id']);
        }
        return $data;
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }
}
