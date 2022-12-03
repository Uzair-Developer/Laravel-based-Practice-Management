<?php

use Laracasts\Flash\Flash;

class ExceptionReasonController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listExceptionReason()
    {
        if (!$this->user->hasAccess('exceptionReason.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['exceptionReasons'] = ExceptionReason::getAll();
        return View::make('exceptionReason/list', $data);
    }

    public function addExceptionReason()
    {
        if (!$this->user->hasAccess('exceptionReason.add') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['exceptionReason'] = array(
            'name' => ''
        );
        return View::make('exceptionReason/add', $data);
    }

    public function createExceptionReason()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, ExceptionReason::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                ExceptionReason::add($inputs);
                Flash::success('Added successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::route('listExceptionReason');
    }

    public function editExceptionReason($id)
    {
        if (!$this->user->hasAccess('exceptionReason.edit') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['exceptionReason'] = ExceptionReason::getById($id);
        return View::make('exceptionReason/add', $data);
    }

    public function updateExceptionReason($id)
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, ExceptionReason::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                ExceptionReason::edit($inputs, $id);
                Flash::success('Updated successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::route('listExceptionReason');
    }

    public function deleteExceptionReason($id)
    {
        if (!$this->user->hasAccess('exceptionReason.delete') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        ExceptionReason::remove($id);
        return Redirect::route('listExceptionReason');
    }
}