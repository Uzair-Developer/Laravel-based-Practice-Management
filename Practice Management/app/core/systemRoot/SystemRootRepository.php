<?php

namespace core\systemRoot;


use SystemRoot;

class SystemRootRepository
{

    public function update($inputs, $id)
    {
        return SystemRoot::where('id',$id)->update($inputs);
    }

    public function getAll()
    {
        return SystemRoot::where('id', 1)->first();
    }

}