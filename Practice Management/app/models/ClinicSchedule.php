<?php

class ClinicSchedule extends Eloquent
{
    protected $table = 'clinic_schedules';
    protected $guarded = array('');

    public static $rules = array(
        'start_date' => "required",
        "end_date" => "required"
    );

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function getByClinicId($clinic_id)
    {
        return self::where('clinic_id', $clinic_id)->first();
    }

    public static function checkDateIsAvailable($date, $clinic_id, $exceptId = '')
    {
        $data = ClinicSchedule::where('clinic_id', $clinic_id)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date);
        if ($exceptId) {
            $data = $data->where('id', '!=', $exceptId);
        }
        return count($data->get()->toArray());
    }

    public static function getAllByClinicId($clinic_id)
    {
        return ClinicSchedule::where('clinic_id', $clinic_id)->get();
    }
}
