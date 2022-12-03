<?php

class Patient extends Eloquent
{
    protected $table = 'patients';
    protected $guarded = array('');

    public static $rules = array(
        "first_name" => "required",
        "phone" => "required",
    );

    public static function add($inputs)
    {
        $inputs['create_timestamp'] = time();
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll()
    {
        return self::paginate(20);
    }

    public static function getAllWithFilter($inputs = null)
    {
        $data = self::where(function ($query) use ($inputs) {
            if ($inputs) {
                if (isset($inputs['name']) && $inputs['name'])
                    $query->whereRaw('REPLACE(LOWER(name),"  "," ") LIKE LOWER("%' . $inputs['name'] . '%")');
                if (isset($inputs['phone']) && $inputs['phone'])
                    $query->where('phone', 'LIKE', "%" . $inputs['phone'] . "%");
                if (isset($inputs['phoneExact']) && $inputs['phoneExact']) {
                    $query->where('phone', $inputs['phoneExact']);
                }
                if (isset($inputs['id']) && $inputs['id']) {
                    $query->where('registration_no', $inputs['id']);
                }
                if (isset($inputs['national_id']) && $inputs['national_id']) {
                    $query->where('national_id', $inputs['national_id']);
                }
                if (isset($inputs['country_id']) && $inputs['country_id']) {
                    $query->where('country_id', $inputs['country_id']);
                }
                if (isset($inputs['city_id']) && $inputs['city_id']) {
                    $query->where('city_id', $inputs['city_id']);
                }
                if (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
                    $query->where('hospital_id', $inputs['hospital_id']);
                }
            }
        });
        if (isset($inputs['getFirst']) && $inputs['getFirst']) {
            $data = $data->first();
        } else {
            $data = $data->paginate(25);
        }
        return $data;
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getByRegistrationNo($id, $hospital_id = '')
    {
        $data = self::where(function ($q) use ($id) {
            $q->where('registration_no', $id);
            $q->orWhere('national_id', $id);
        });
        if ($hospital_id) {
            $data = $data->where('hospital_id', $hospital_id);
        }
        return $data->orderBy('id', 'desc')->first();
    }

    public static function getByPinNo($id, $hospital_id = '', $inputs = [])
    {
        $data = self::where('registration_no', $id);
        if ($hospital_id) {
            $data = $data->where('hospital_id', $hospital_id);
        }
        if (isset($inputs['phone']) && $inputs['phone']) {
            $data = $data->where('phone', $inputs['phone']);
        }
        return $data->orderBy('id', 'desc')->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function getPhone($id)
    {
        return self::where('id', $id)->pluck('phone');
    }

    public static function getRegistrationNo($id)
    {
        return self::where('id', $id)->pluck('registration_no');
    }

    public static function getNationalId($id)
    {
        return self::where('id', $id)->pluck('national_id');
    }

    public static function getRefId($search, $searchById = false, $hospital_id = '', $searchByNationalId = '')
    {
        $data = self::where(function ($q) use ($search, $searchById, $searchByNationalId) {
            if ($searchById == 'false' || $searchById === false) {
                if ($searchByNationalId != 'false') {
                    $q->where('national_id', $search);
                } else {
                    $q->where('registration_no', $search);
                }
            } else {
                $q->where('id', $search);
            }
        });
        if ($hospital_id) {
            $data = $data->where('hospital_id', $hospital_id);
        }
        return $data->orderBy('id', 'desc')->first();
    }

    public static function searchPatient($inputs)
    {
        if ((isset($inputs['name']) && $inputs['name']) || (isset($inputs['phone']) && $inputs['phone'])
            || (isset($inputs['id']) && $inputs['id'])
            || (isset($inputs['registration_no']) && $inputs['registration_no'])
            || (isset($inputs['national_id']) && $inputs['national_id'])
        ) {
            return self::where(function ($query) use ($inputs) {
                if (isset($inputs['name']) && $inputs['name'])
                    $query->whereRaw('REPLACE(LOWER(name),"  "," ") LIKE LOWER("%' . $inputs['name'] . '%")');
                if (isset($inputs['phone']) && $inputs['phone'])
                    $query->where('phone', 'LIKE', "%" . $inputs['phone'] . "%");
                if (isset($inputs['id']) && $inputs['id']) {
                    $query->where('registration_no', $inputs['id']);
                }
                if (isset($inputs['registration_no']) && $inputs['registration_no']) {
                    $query->where('registration_no', 'LIKE', '%' . $inputs['registration_no'] . '%');
                }
                if (isset($inputs['national_id']) && $inputs['national_id']) {
                    $query->where('national_id', $inputs['national_id']);
                }
            })->orderBy('id', 'desc')->lists('id');
        } else {
            return array();
        }
    }

    public static function getByCallerId($caller_id)
    {
        return self::where('caller_id', $caller_id)->get()->toArray();
    }

    public static function getPatientIdAutoComplete($q, $hospital_id = '')
    {
        $data = self::where(function ($query) use ($q) {
            $query->whereRaw('REPLACE(LOWER(name),"  "," ") LIKE LOWER("%' . $q . '%")')->orWhere('registration_no', $q);
        });
        if ($hospital_id) {
            $data = $data->where('hospital_id', $hospital_id);
        }
        return $data->orderBy('id', 'asc')->limit(10)->lists('name', 'registration_no');
    }

    public static function getPatientIdByPhone($phone, $hospital_id = '')
    {
        $data = self::where('phone', 'LIKE', $phone . '%');
        if ($hospital_id) {
            $data = $data->where('hospital_id', $hospital_id);
        }
        return $data->orderBy('id', 'asc')->limit(10)->lists('phone', 'id');
    }

    public static function getPatientIdByNationalId($national_id, $hospital_id = '')
    {
        $data = self::where('national_id', 'LIKE', $national_id . '%');
        if ($hospital_id) {
            $data = $data->where('hospital_id', $hospital_id);
        }
        return $data->orderBy('id', 'asc')->limit(10)->lists('national_id', 'id');
    }

    public static function getPatientIdByIdsAutoComplete($q, $hospital_id = '')
    {
        $patientIds = Reservation::getPatientIdsByDate(date('Y-m-d'));
        $data = self::where(function ($query) use ($q) {
            $query->whereRaw('REPLACE(LOWER(name),"  "," ") LIKE LOWER("%' . $q . '%")')->orWhere('registration_no', 'LIKE', $q . '%');
        });
        if ($hospital_id) {
            $data = $data->where('hospital_id', $hospital_id);
        }
        return $data->whereIn('id', $patientIds)->orderBy('id', 'desc')->lists('name', 'registration_no');
    }

    public static function getNationalIdIfNull()
    {
        self::whereNull('national_id')->chunk(25, function ($patients) {
//            dd($patients->toArray());
            foreach ($patients as $key => $val) {
                $patient = RiyadhPatient::getByRegNo($val['registration_no']);
//                dd($patient->toArray());
                if($patient['IDENTIFICATIONNO']) {
                    $array['national_id'] = $patient['IDENTIFICATIONNO'];
                } else {
                    $array['national_id'] = '0000000000';
                }
                self::edit($array, $val['id']);
            }
        });
    }

}
