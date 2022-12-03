<?php

namespace core\currency;


use Currency;

class CurrencyRepository
{

    public function save($inputs)
    {
        return Currency::create($inputs)->toArray();
    }

    public function update($inputs, $id)
    {
        return Currency::where('id',$id)->update($inputs);
    }

    public function getAll()
    {
        return Currency::all()->toArray();
    }

}