<?php


class PatientInstruction extends Eloquent
{
    public static $rules = array(
        'patient_instruction_en' => "required",
        'patient_instruction_ar' => "required",
    );
    protected $table = 'website_patient_instruction';
    protected $guarded = array('');

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }
}
