<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;

class SecurityController extends BaseController
{
    public $user = '';

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function addSecurity($id)
    {
        if ($this->user->user_type_id != 1 && $this->user->user_type_id != 2) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['user'] = Sentry::findUserById($id);
        return View::make('security/add', $data);
    }

    public function createSecurity($id)
    {
        $inputs = Input::except('_token');
        $permissions = array();
        foreach ($inputs as $key => $val) {
            if (!empty($val[0])) {
                foreach ($val as $permission) {
                    $permissions[$key . '.' . $permission] = 1;
                }
            }
        }
        User::edit(array('permissions' => ''), $id);
        $user = Sentry::findUserById($id);
        $user->permissions = $permissions;
        if ($user->save()) {
            Flash::success('Updated Successfully');
            return Redirect::route('users');
        } else {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput();
        }
    }

    public function listGroup()
    {
        if ($this->user->user_type_id != 1 && $this->user->user_type_id != 2 && !$this->user->hasAccess('permissions.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['groups'] = Group::getAll();
        return View::make('group/list', $data);
    }

    public function addGroup()
    {
        if ($this->user->user_type_id != 1 && $this->user->user_type_id != 2 && !$this->user->hasAccess('permissions.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['name'] = '';
        $data['in_filter'] = '';
        $data['system'] = '';
        $data['permissions'] = array();
        return View::make('group/add', $data);
    }

    public function createGroup()
    {
        $inputs = Input::except('_token');
        $rules = array(
            'name' => "required|unique:groups"
        );
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            Flash::success($validator->messages());
            return Redirect::route('listGroup');
        }
        $group_name = $inputs['name'];
        $system = isset($inputs['system']) ? 1 : 0;
        $inFilter = isset($inputs['in_filter']) ? 1 : 2;
        unset($inputs['name']);
        unset($inputs['system']);
        unset($inputs['in_filter']);
        $permissions = array();
        foreach ($inputs as $key => $val) {
            if (!empty($val[0])) {
                foreach ($val as $permission) {
                    $permissions[$key . '.' . $permission] = 1;
                }
            }
        }
        $group = array(
            'name' => $group_name,
            'permissions' => $permissions,
            'system' => $system,
            'in_filter' => $inFilter,
        );
        Sentry::createGroup($group);
        Flash::success('Added Successfully');
        return Redirect::route('listGroup');
    }

    public function editGroup($id)
    {
        if ($this->user->user_type_id != 1 && $this->user->user_type_id != 2 && !$this->user->hasAccess('permissions.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $group = Sentry::findGroupById($id);
        $groupPermissions = $group->getPermissions();
        $data['name'] = $group['name'];
        $data['in_filter'] = $group['in_filter'];
        $data['system'] = $group['system'];
        $data['permissions'] = $groupPermissions;
        return View::make('group/add', $data);
    }

    public function updateGroup($id)
    {
        $inputs = Input::except('_token');
        $rules = array(
            'name' => "required|unique:groups,name,$id"
        );
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::route('listGroup');
        }
        $group_name = $inputs['name'];
        $system = isset($inputs['system']) ? 1 : 0;
        $inFilter = isset($inputs['in_filter']) ? 1 : 2;
        unset($inputs['name']);
        unset($inputs['system']);
        unset($inputs['in_filter']);
        $permissions = array();
        foreach ($inputs as $key => $val) {
            if (!empty($val[0])) {
                foreach ($val as $permission) {
                    $permissions[$key . '.' . $permission] = 1;
                }
            }
        }
        Group::edit(array('permissions' => ''), $id);
        $group = Sentry::findGroupById($id);
        $group->name = $group_name;
        $group->permissions = $permissions;
        $group->system = $system;
        $group->in_filter = $inFilter;

        if ($group->save()) {
            Flash::success('Added Successfully');
        } else {
            Flash::error('Ops, try again later!');
        }
        return Redirect::route('listGroup');
    }

    public function deleteGroup($id)
    {
        if ($this->user->user_type_id != 1 && $this->user->user_type_id != 2 && !$this->user->hasAccess('permissions.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $group = Sentry::findGroupById($id);
        $group->delete();
        UserGroup::removeByGroupId($id);
        return Redirect::route('listGroup');
    }

}