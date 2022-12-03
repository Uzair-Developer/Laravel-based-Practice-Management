<?php

namespace core\hospitalContact;


use HospitalContact;

class HospitalContactRepository
{

    public function save($inputs)
    {
        return HospitalContact::create($inputs)->toArray();
    }

    public function update($inputs, $id)
    {
        return HospitalContact::where('id',$id)->update($inputs);
    }

    public function getByHospitalId($hospitalId)
    {
        return HospitalContact::where('hospital_id', $hospitalId)->get()->toArray();
    }

    public function getById($id)
    {
        return HospitalContact::where('id',$id)->first();
    }

    public function delete($id)
    {
        return HospitalContact::where('id',$id)->delete();
    }

}