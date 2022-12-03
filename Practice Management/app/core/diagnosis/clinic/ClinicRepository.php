<?php
namespace core\diagnosis\clinic;

use dClinic;

class ClinicRepository
{

    public function save($inputs)
    {
        return dClinic::create($inputs);
    }

    public function update($inputs, $id)
    {
        return dClinic::where('id', $id)->update($inputs);
    }

    public function getAll()
    {
        return dClinic::all()->toArray();
    }

    public function getNameById($id)
    {
        return dClinic::where('id', $id)->pluck('name');
    }

    public function getById($id)
    {
        return dClinic::where('id', $id)->first()->toArray();
    }

    public function delete($id)
    {
        dClinic::where('id', $id)->delete();
    }

}