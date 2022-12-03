<?php
namespace core\diagnosis\disease;

use Disease;
use Specialty;

class DiseaseRepository
{

    public function save($inputs)
    {
        return Disease::create($inputs);
    }

    public function update($inputs, $id)
    {
        return Disease::where('id', $id)->update($inputs);
    }

    public function getAll()
    {
        $data = Disease::all()->toArray();
        foreach ($data as $key => $val) {
            $data[$key]['specialty_name'] = Specialty::getNameById($val['specialty_id']);
        }
        return $data;
    }

    public function getAllPaginate()
    {
        $data = Disease::paginate(20);
        foreach ($data as $key => $val) {
            $data[$key]['specialty_name'] = Specialty::getNameById($val['specialty_id']);
        }
        return $data;
    }

    public function getAllPaginateWithFilter($q)
    {
        $data = Disease::where(function ($query) use ($q) {
            $query->where('name', 'LIKE', "%" . $q . "%")->orWhere('id_ref', 'LIKE', "%" . $q . "%");
        })->paginate(20);
        foreach ($data as $key => $val) {
            $data[$key]['specialty_name'] = Specialty::getNameById($val['specialty_id']);
        }
        return $data;
    }

    public function getAutoComplete($q)
    {
        return Disease::where(function ($query) use ($q) {
            $query->where('name', 'LIKE', "%" . $q . "%")->orWhere('id_ref', 'LIKE', "%" . $q . "%");
        })->lists('name', 'id');
    }

    public function getNameById($id)
    {
        return Disease::where('id', $id)->pluck('name');
    }

    public function getById($id)
    {
        return Disease::where('id', $id)->first();
    }

    public function delete($id)
    {
        Disease::where('id', $id)->delete();
    }

}