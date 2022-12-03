<?php
//use core\diagnosis\enums\DiseaseSymptomsStatus;

class Disease extends Eloquent
{
    protected $table = 'diagnosis_diseases';
    protected $guarded = array('');

    public function symptoms()
    {
        return $this->belongsToMany('Symptom', 'diagnosis_disease_symptoms', 'disease_id', 'symptom_id')
            ->withPivot('rate');
    }

    public function questions()
    {
        return $this->hasMany('DiseaseQuestions', 'disease_id', 'id');
    }

    public static function getAll($inputs = '')
    {
        $data = self::whereRaw('1 = 1');

        if (isset($inputs['name']) && $inputs['name']) {
            $data = $data->whereRaw('LOWER(name) LIKE LOWER("' . $inputs['name'] . '%")');
        }
        if (isset($inputs['limit']) && $inputs['limit']) {
            $data = $data->limit($inputs['limit']);
        }
        if (isset($inputs['namesOnly']) && $inputs['namesOnly']) {
            $data = $data->lists('name');
        } else {
            $data = $data->get()->toArray();
        }
        return $data;
    }

    public static function getWithQuestions($diseaseArray, $age, $gender)
    {
        return self::with('questions')->whereIn('id', $diseaseArray)
            ->where('age_from', '<=', $age)
            ->where('age_to', '>=', $age)
            ->where(function ($query) use ($gender) {
                $query->where('gender', 3)->orWhere('gender', $gender);;
            })->get()->toArray();
    }

    public static function getWithSymptoms($diseaseArray, $symptomsArray)
    {
        return self::with(array('symptoms' => function ($q) use ($symptomsArray, $diseaseArray) {
            $q->whereNotIn('symptom_id', $symptomsArray)
                ->whereIn('disease_id', $diseaseArray)
                ->wherePivot('status', DiseaseSymptomsStatus::approval);
        }))->whereIn('id', $diseaseArray)->get()->toArray();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function getIdByName($name)
    {
        return self::where('name', $name)->pluck('id');
    }
}
