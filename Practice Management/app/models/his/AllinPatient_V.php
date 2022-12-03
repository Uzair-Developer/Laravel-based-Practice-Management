<?php

class AllinPatient_V extends Eloquent
{
    protected $table = 'dbo.AllinPatient_V';
    protected $guarded = array('');
    protected $connection = 'sqlsrv';
    public $timestamps = false;

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
        $data = self::whereRaw('1 = 1');
        if (isset($inputs['ipid']) && $inputs['ipid']) {
            $data = $data->where('ipid', $inputs['ipid']);
        }
        if (isset($inputs['ipids']) && $inputs['ipids']) {
            $data = $data->whereIn('ipid', $inputs['ipids']);
        }
        if (isset($inputs['not_ipids']) && $inputs['not_ipids']) {
            $data = $data->whereNotIn('ipid', $inputs['not_ipids']);
        }
        if (isset($inputs['from_admitdatetime']) && $inputs['from_admitdatetime']) {
            $data = $data->where('admitdatetime', '>=', $inputs['from_admitdatetime']);
        }
        if (isset($inputs['discharge']) && $inputs['discharge']) {
            $data = $data->where('DISCHARG_FLAG', $inputs['discharge']);
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

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function getLast3Month()
    {
        $last3Month = date('Y-m-d', strtotime('-30 days ' . date('Y-m-d')));
        self::where('DISCHARG_FLAG', 'Y')
            ->where('admitdatetime', '>=', $last3Month . ' 00:00:00.000')
            ->chunk(25, function ($patients) {
                foreach ($patients as $key => $val) {
//                    dd($patients->toArray());
                    $patient = Patient::getByRegistrationNo($val['RegistrationNo']);
                    if ($patient) {
                        $physician = User::checkHisExist($val['DoctorID']);
                        if (empty(InPatient::checkExist($patient['id'], $val['ipid']))) {
                            $array = [
                                'patient_id' => $patient['id'],
                                'registration_no' => $val['RegistrationNo'],
                                'ipid' => $val['ipid'],
                                'admitdatetime' => $val['admitdatetime'],
                                'physician_id' => isset($physician['id']) ? $physician['id'] : null,
                                'physician_his_id' => $val['DoctorID'],
                            ];
                            InPatient::add($array);
                        }
                    }
                }
            });
    }
}
