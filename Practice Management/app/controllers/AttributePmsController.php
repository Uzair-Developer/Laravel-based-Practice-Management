<?php

use core\enums\AttributeType;
use Laracasts\Flash\Flash;

class AttributePmsController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listAttributePms()
    {
        if (!$this->user->hasAccess('AttributePms.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        if ($inputs) {
            $type = isset($inputs['type']) ? $inputs['type'] : '';
            $data['attributePms'] = AttributePms::getAll($type);
        } else {
            $data['attributePms'] = AttributePms::getAll();
        }
        $data['attributePmsTypes'] = AttributeType::$pms;
        $data['parentReferredTo'] = AttributePms::getPatentReferredTo();
        return View::make('attributePms/list', $data);
    }

    public function createAttributePms()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, AttributePms::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                if ($inputs['type_id'] != 1 && isset($inputs['effect'])) {
                    unset($inputs['effect']);
                }
                if ($inputs['type_id'] != 5 && isset($inputs['duration'])) {
                    unset($inputs['duration']);
                }
                if ($inputs['type_id'] != 12 && isset($inputs['parent_id'])) {
                    unset($inputs['parent_id']);
                }
                AttributePms::add($inputs);
                Flash::success('Added successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::back();
    }

    public function updateAttributePms()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, AttributePms::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                $id = $inputs['id'];
                unset($inputs['id']);
                if ($inputs['type_id'] != 1 && isset($inputs['effect'])) {
                    unset($inputs['effect']);
                }
                if ($inputs['type_id'] != 5 && isset($inputs['duration'])) {
                    unset($inputs['duration']);
                }
                if ($inputs['type_id'] != 12 && isset($inputs['parent_id'])) {
                    unset($inputs['parent_id']);
                }
                AttributePms::edit($inputs, $id);
                Flash::success('Updated successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::back();
    }

    public function deleteAttributePms($id)
    {
        if (!$this->user->hasAccess('AttributePms.delete') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $exceptions = PhysicianException::checkReasonExist($id);
        if ($exceptions) {
            Flash::error('their is related records with this attribute');
            return Redirect::route('listAttributePms');
        }
        AttributePms::remove($id);
        return Redirect::route('listAttributePms');
    }

    public function getAttributePms()
    {
        $id = Input::get('id');
        return AttributePms::getById($id);
    }

    public function getChildReferredTo()
    {
        $child = AttributePms::getByPatentReferredTo(Input::get('id'));
        $html = '<option value="">Choose</option>';
        foreach ($child as $key => $val) {
            $html .= '<option value="' . $val['id'] . '">' . $val['name'] . '</option>';
        }
        return $html;
    }
}