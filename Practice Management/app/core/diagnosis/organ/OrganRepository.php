<?php
namespace core\diagnosis\organ;

use Organ;

class OrganRepository
{

    public function save($inputs)
    {
        return Organ::create($inputs);
    }

    public function update($inputs, $id)
    {
        unset($inputs['_token']);
        return Organ::where('id', $id)->update($inputs);
    }

    public function getAll()
    {
        return Organ::all()->toArray();
    }

    public function getNameById($id)
    {
        return Organ::where('id', $id)->pluck('name');
    }

    public function getById($id)
    {
        return Organ::where('id', $id)->first()->toArray();
    }

    public function delete($id)
    {
        Organ::where('id', $id)->delete();
    }

    public function getIdsByName($name)
    {
        return Organ::where('name', 'LIKE', "%" . $name . "%")->lists('id');
    }

}