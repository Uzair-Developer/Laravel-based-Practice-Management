<?php


use core\diagnosis\disease\DiseaseRepository;
use core\diagnosis\enums\DiseaseSymptomsStatus;

class DiseaseSymptoms extends Eloquent
{
    protected $table = 'diagnosis_disease_symptoms';
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
        return self::where('id', $id)->update($inputs);
    }


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
        $data = self::where('symptom_id', $id)->where('status', DiseaseSymptomsStatus::approval)->get()->toArray();
        $diRepo = new DiseaseRepository();
        foreach ($data as $key => $val) {
            $data[$key]['disease_name'] = $diRepo->getNameById($val['disease_id']);
        }
        return $data;
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getAll()
    {
        $data = self::paginate(20);
        foreach ($data as $key => $val) {
            $data[$key]['disease_name'] = Disease::getName($val['disease_id']);
            $data[$key]['symptom_name'] = Symptom::getName($val['symptom_id']);
            $data[$key]['user_name'] = User::getName($val['user_id']);
        }
        return $data;
    }
    public static function getAllWithFilter($inputs)
    {
        $data = self::where(function($query) use ($inputs){
            if($inputs['status'] != ''){
                $query->where('status', $inputs['status']);
            }
        })->paginate(20);
        foreach ($data as $key => $val) {
            $data[$key]['disease_name'] = Disease::getName($val['disease_id']);
            $data[$key]['symptom_name'] = Symptom::getName($val['symptom_id']);
            $data[$key]['user_name'] = User::getName($val['user_id']);
        }
        return $data;
    }

    public static function getPending()
    {
        return self::where('status', DiseaseSymptomsStatus::pending)->get()->toArray();
    }

    public static function checkExist($disease_id, $symptom_id)
    {
        return self::where('disease_id', $disease_id)->where('symptom_id', $symptom_id)->first();
    }

    public static function getAllByUser($user_id)
    {
        $data = self::where('status', '!=', DiseaseSymptomsStatus::approval)->where('user_id', $user_id)->paginate(10);
        foreach ($data as $key => $val) {
            $data[$key]['disease_name'] = Disease::getName($val['disease_id']);
            $data[$key]['symptom_name'] = Symptom::getName($val['symptom_id']);
            $data[$key]['user_name'] = User::getName($val['user_id']);
        }
        return $data;
    }
}
