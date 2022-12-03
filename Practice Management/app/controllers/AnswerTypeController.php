<?php

class AnswerTypeController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listAnswerType()
    {
        if (!$this->user->hasAccess('answerType.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $inputs['paginate'] = 20;
        $data['answerTypes'] = AnswerType::getAll($inputs);
        $data['types'] = [1 => "Single selection", 2 => "Multi Selection"];
        return View::make('answerTypes/list', $data);
    }

    public function createAnswerType()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, AnswerType::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                AnswerType::add($inputs);
                Flash::success('Added successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::back();
    }

    public function updateAnswerType()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, AnswerType::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                $id = $inputs['id'];
                AnswerType::edit($inputs, $id);
                Flash::success('Updated successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::back();
    }

    public function deleteAnswerType($id)
    {
        if (!$this->user->hasAccess('answerType.delete') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        AnswerType::remove($id);
        Flash::success('Deleted successfully');
        return Redirect::route('listAnswerType');
    }

    public function getAnswerType()
    {
        $id = Input::get('id');
        return AnswerType::getById($id);
    }
}
