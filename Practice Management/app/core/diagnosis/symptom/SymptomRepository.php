<?php
namespace core\diagnosis\symptom;

use core\diagnosis\organ\OrganRepository;
use Symptom;

class SymptomRepository
{

    public function save($inputs)
    {
        $row = Symptom::getLastRow();
        $inputs['id_ref'] = self::increment($row['id_ref']);
        return Symptom::create($inputs);
    }

    function increment($string)
    {
        return preg_replace_callback('/^([^0-9]*)([0-9]+)([^0-9]*)$/', function ($m) {
            return $m[1] . str_pad($m[2] + 1, strlen($m[2]), '0', STR_PAD_LEFT) . $m[3];
        }, $string);
    }

    public function update($inputs, $id)
    {
        return Symptom::where('id', $id)->update($inputs);
    }

    public function getAll()
    {
        $data = Symptom::all()->toArray();
        $organRepo = new OrganRepository();
        foreach ($data as $key => $val) {
            $data[$key]['organ_name'] = $organRepo->getNameById($val['organ_id']);
        }
        return $data;
    }

    public function getAllPaginate()
    {
        $data = Symptom::paginate(20);
        $organRepo = new OrganRepository();
        foreach ($data as $key => $val) {
            $data[$key]['organ_name'] = $organRepo->getNameById($val['organ_id']);
        }
        return $data;
    }

    public function getAllPaginateWithFilter($q)
    {
        $data = Symptom::where(function ($query) use ($q) {
            if ($q['symptom']){
                $query->where('name', 'LIKE', "%" . $q['symptom'] . "%")->orWhere('id_ref', 'LIKE', "%" . $q['symptom'] . "%");
            }
            if ($q['organ']){
                $organRepo = new OrganRepository();
                $organsId = $organRepo->getIdsByName($q['organ']);
                $query->whereIn('organ_id', $organsId);
            }
        })->paginate(20);
        $organRepo = new OrganRepository();
        foreach ($data as $key => $val) {
            $data[$key]['organ_name'] = $organRepo->getNameById($val['organ_id']);
        }
        return $data;
    }

    public function getNameById($id)
    {
        return Symptom::where('id', $id)->pluck('name');
    }

    public function getById($id)
    {
        return Symptom::where('id', $id)->first()->toArray();
    }

    public function delete($id)
    {
        Symptom::where('id', $id)->delete();
    }

    public function getAutoComplete($q)
    {
        return Symptom::where(function ($query) use ($q) {
            $query->where('name', 'LIKE', "%" . $q . "%")->orWhere('id_ref', 'LIKE', "%" . $q . "%");
        })->lists('name', 'id');
    }

}