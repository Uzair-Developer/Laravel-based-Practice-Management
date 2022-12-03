<?php


class InstructionController extends BaseController
{

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }
    
    public function editInstruction($id)
    {
        if(!$this->user->hasAccess('instruction.list') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['instruction'] = Instruction::getById($id);
        return View::make('diagnosis/instruction.edit', $data);
    }

    public function updateInstruction($id)
    {
        Instruction::edit(Input::except('_token'), $id);
        Flash::success('Updated Successfully');
        return Redirect::route('editInstruction', $id);
    }
}
