<?php

namespace core\publicHoliday;


use PublicHoliday;

class PublicHolidayRepository
{

    public function save($inputs)
    {
        return PublicHoliday::create($inputs)->toArray();
    }

    public function update($inputs, $id)
    {
        return PublicHoliday::where('id', $id)->update($inputs);
    }

    public function getAll()
    {
        $data = PublicHoliday::all()->toArray();
        foreach ($data as $key => $val) {
            $data[$key]['hospital_name'] = \Hospital::getName($val['hospital_id']);
        }
        return $data;
    }

    public function getById($id)
    {
        return PublicHoliday::where('id', $id)->first();
    }

    public function delete($id)
    {
        return PublicHoliday::where('id', $id)->delete();
    }

}