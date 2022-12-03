<?php

class PmsDiagnosis extends Eloquent
{
    protected $table = 'pms_diagnosis';
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
        $data = self::whereRaw(' 1 = 1 ');
        if ((isset($inputs['patient_name']) && $inputs['patient_name']) || (isset($inputs['patient_phone']) && $inputs['patient_phone'])
            || (isset($inputs['patient_pin']) && $inputs['patient_pin'])
        ) {
            $patientArray = array(
                'name' => isset($inputs['patient_name']) ? $inputs['patient_name'] : null,
                'registration_no' => isset($inputs['patient_pin']) ? $inputs['patient_pin'] : null,
                'phone' => isset($inputs['patient_phone']) ? $inputs['patient_phone'] : null,
            );
            $patients_id = Patient::searchPatient($patientArray);
            $data = $data->whereIn('patient_id', $patients_id);
        }
        if (isset($inputs['main_system_affected_id']) && $inputs['main_system_affected_id']) {
            $data = $data->where('main_system_affected_id', $inputs['main_system_affected_id']);
        }
        if (isset($inputs['referred_to_parent_id']) && $inputs['referred_to_parent_id']) {
            $data = $data->where('referred_to_parent_id', $inputs['referred_to_parent_id']);
        }
        if (isset($inputs['referred_to_child_id']) && $inputs['referred_to_child_id']) {
            $data = $data->where('referred_to_child_id', $inputs['referred_to_child_id']);
        }
        if (isset($inputs['organ_id']) && $inputs['organ_id']) {
            $data = $data->where(function ($q) use ($inputs) {
                $q->where('organ_id', $inputs['organ_id']);
                $q->orWhere('organ_id', 'LIKE', $inputs['organ_id'] . '%');
                $q->orWhere('organ_id', 'LIKE', '%' . $inputs['organ_id'] . '%');
                $q->orWhere('organ_id', 'LIKE', '%' . $inputs['organ_id']);
            });
        }
        if (isset($inputs['paginate']) && $inputs['paginate']) {
            $data = $data->paginate(25);
        } else {

            $data = $data->get();
        }
        if (isset($inputs['details']) && $inputs['details']) {
            foreach ($data as $key => $val) {
                $patient = Patient::getById($val['patient_id']);
                $data[$key]['patient_name'] = $patient['name'];
                $data[$key]['patient_phone'] = $patient['phone'];
                $data[$key]['patient_id'] = $patient['registration_no'];
                $data[$key]['main_system_affected_name'] = AttributePms::getName($val['main_system_affected_id']);
                $data[$key]['organ_name'] = '';
                if ($val['organ_id']) {
                    $organs = explode(',', $val['organ_id']);
                    foreach ($organs as $key2 => $val2) {
                        if (count($organs) == $key2 + 1) {
                            $data[$key]['organ_name'] .= Organ::getName($val2);
                        } else {
                            $data[$key]['organ_name'] .= Organ::getName($val2) . ', ';
                        }
                    }
                }
                $data[$key]['referred_to_parent_name'] = AttributePms::getName($val['referred_to_parent_id']);
                $data[$key]['referred_to_child_name'] = AttributePms::getName($val['referred_to_child_id']);
                $data[$key]['created_by_name'] = User::getName($val['created_by']);
                $data[$key]['updated_by_name'] = User::getName($val['updated_by']);
            }
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
}
