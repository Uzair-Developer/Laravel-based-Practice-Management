<?php


class DiseaseQuestions extends Eloquent
{
    protected $table = 'diagnosis_disease_questions';
    protected $guarded = array('');

//    public static  $rules = array(
//        "name" => "required"
//    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id',$id)->update($inputs);
    }

    public static function getByDiseaseId($id)
    {
        return self::where('disease_id', $id)->get()->toArray();
    }

    public static function getIdsByDiseaseId($id)
    {
        return self::where('disease_id', $id)->lists('id');
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('text');
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }
}
