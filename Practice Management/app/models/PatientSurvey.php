<?php

class PatientSurvey extends Eloquent
{
    protected $table = 'patient_survey';
    protected $guarded = array('');

    public static $rules = array(
        'patient_id' => "required",
    );

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
        $data = self::select('patient_survey.*')->where(function ($q) use ($inputs) {
            if (isset($inputs['reservation_id']) && $inputs['reservation_id']) {
                $q->where('reservation_id', $inputs['reservation_id']);
            }
            if (isset($inputs['survey_id']) && $inputs['survey_id']) {
                $q->where('survey_id', $inputs['survey_id']);
            }
            if (isset($inputs['patient_id']) && $inputs['patient_id']) {
                $q->where('patient_id', $inputs['patient_id']);
            }
            if (isset($inputs['code']) && $inputs['code']) {
                $Ids = Reservation::getByPatientsIdAndDates(['code' => $inputs['code'], 'getIds' => true]);
                $q->whereIn('reservation_id', $Ids);
            }
            if ((isset($inputs['name']) && $inputs['name']) || (isset($inputs['phone']) && $inputs['phone'])
                || (isset($inputs['id']) && $inputs['id'])
                || (isset($inputs['national_id']) && $inputs['national_id'])
            ) {
                $patients_id = Patient::searchPatient($inputs);
                $q->whereIn('patient_id', $patients_id);
            }
            if (isset($inputs['date_from']) && $inputs['date_from']) {
                $q->where(DB::raw('date(created_at)'), '>=', $inputs['date_from']);
            }
            if (isset($inputs['date_to']) && $inputs['date_to']) {
                $q->where(DB::raw('date(created_at)'), '<=', $inputs['date_to']);
            }
        });
        if (isset($inputs['reservation_ids']) && $inputs['reservation_ids']) {
            $data = $data->join('reservations', function ($join) use ($inputs) {
                $join->on('reservations.id', '=', 'patient_survey.reservation_id');
                if (isset($inputs['clinic_id']) && $inputs['clinic_id']) {
                    $join->where('reservations.clinic_id', '=', $inputs['clinic_id']);
                }
                if (isset($inputs['physician_id']) && $inputs['physician_id']) {
                    $join->where('reservations.physician_id', '=', $inputs['physician_id']);
                }
                if (isset($inputs['res_date_from']) && $inputs['res_date_from']) {
                    $join->where('reservations.date', '>=', $inputs['res_date_from']);
                }
                if (isset($inputs['res_date_to']) && $inputs['res_date_to']) {
                    $join->where('reservations.date', '<=', $inputs['res_date_to']);
                }
            });
        }
        if (isset($inputs['paginate'])) {
            $data = $data->paginate($inputs['paginate']);
        } elseif (isset($inputs['getIds']) && $inputs['getIds']) {
            $data = $data->lists('patient_survey.id');
        } elseif (isset($inputs['getFirst']) && $inputs['getFirst']) {
            $data = $data->first();
        } else {
            $data = $data->get();
        }
        if (isset($inputs['details'])) {
            foreach ($data as $k => $v) {
                $survey = Survey::getById($v['survey_id']);
                $reservation = Reservation::getById($v['reservation_id']);
                $patient = Patient::getById($v['patient_id']);
                $data[$k]['survey_title'] = $survey['header_en'];
                $data[$k]['reservation_code'] = $reservation['code'];
                $data[$k]['patient'] = $patient;
                $data[$k]['patient_name'] = $patient['name'];
                $data[$k]['patient_phone'] = $patient['phone'];
                $data[$k]['national_id'] = $patient['national_id'];
                $data[$k]['surveyDetails'] = PatientSurveyDetails::getAll(['patient_survey_id' => $v['survey_id'], 'details' => true]);
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
