<?php


class PatientEvents extends Eloquent
{
    protected $table = 'diagnosis_patient_events';
    protected $guarded = array('');

//    public static  $rules = array(
//        "name" => "required",
//    );

    public function eventDetails()
    {
        return $this->hasMany('EventDetails', 'event_id');
    }

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id',$id)->update($inputs);
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getNameById($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getEvents($patientId)
    {
        return self::with('eventDetails')->where('patient_id', $patientId)->get()->toArray();
    }
}
