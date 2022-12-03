<?php


class Specialty extends Eloquent
{
    protected $table = 'diagnosis_clinic_specialties';
    protected $guarded = array('');

    public static  $rules = array(
        "name" => "required"
    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id',$id)->update($inputs);
    }

    public static function getAll()
    {
        $data = self::all()->toArray();
        foreach ($data as $key => $val) {
            $data[$key]['clinic_name'] = Clinic::getNameById($val['clinic_id']);
        }
        return $data;
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
}
