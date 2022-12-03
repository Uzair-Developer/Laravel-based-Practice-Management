<?php

use core\diagnosis\enums\DiseaseAttr;
use core\diagnosis\enums\DiseaseSymptomsStatus;

class ViewDiseaseSymptoms extends Eloquent
{
    protected $table = 'viewDiseaseSymptoms';
    protected $guarded = array('');

//    public static  $rules = array(
//        "name" => "required"
//    );


    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getByDiseaseId($id)
    {
        return self::where('disease_id', $id)->get()->toArray();
    }

    public static function getSymptomsByDiseaseId($id)
    {
        return self::where('disease_id', $id)->lists('symptom_id');
    }

    public static function getIdsByDiseaseId($id)
    {
        return self::where('disease_id', $id)->lists('id');
    }

    public static function getNameById($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function getDiseasesBySymptom($id)
    {
        if (Session::has('patient')) {
            $patientInfo = Session::get('patient');
            return self::where('symptom_id', $id)
                ->where('status', DiseaseSymptomsStatus::approval)
                ->where('age_from', '<=', $patientInfo['age'])
                ->where('age_to', '>=', $patientInfo['age'])
                ->where('type', '!=', DiseaseAttr::$typeReturn['rare'])
                ->where(function ($query) use ($patientInfo) {
                    $query->where('gender', 3)->orWhere('gender', $patientInfo['gender']);
                })->orderBy('type')->get()->toArray();
        } else {
            return self::where('symptom_id', $id)->where('status', DiseaseSymptomsStatus::approval)->get()->toArray();
        }
    }

    public static function getByDiseasesAndSymptom($disease_id, $symptom_id)
    {
        return self::where('disease_id', $disease_id)->where('symptom_id', $symptom_id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }
}
