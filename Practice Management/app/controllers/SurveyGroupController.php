<?php

class SurveyGroupController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listSurveyGroup()
    {
        if (!$this->user->hasAccess('surveyGroup.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $inputs['paginate'] = 20;
        $data['surveyGroups'] = SurveyGroup::getAll($inputs);
        return View::make('surveyGroups/list', $data);
    }

    public function createSurveyGroup()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, SurveyGroup::$rules);
        if ($validator->fails()) {
            return json_encode(["msg" => $validator->messages()]);
        } else {
            try {
                SurveyGroup::add($inputs);
                return json_encode(["msg" => 'Added successfully']);
            } catch (Exception $e) {
                return json_encode(["msg" => 'Ops, try again later!']);
            }
        }
    }

    public function updateSurveyGroup()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, SurveyGroup::$rules);
        if ($validator->fails()) {
            return json_encode(["msg" => $validator->messages()]);
        } else {
            try {
                $id = $inputs['id'];
                unset($inputs['id']);
                SurveyGroup::edit($inputs, $id);
                return json_encode(["msg" => 'Updated successfully']);
            } catch (Exception $e) {
                return json_encode(["msg" => 'Ops, try again later!']);
            }
        }
    }

    public function deleteSurveyGroup($id)
    {
        if (!$this->user->hasAccess('surveyGroup.delete') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        SurveyGroup::remove($id);
        Flash::success('Deleted successfully');
        return Redirect::route('listSurveyGroup');
    }

    public function getSurveyGroup()
    {
        $id = Input::get('id');
        return SurveyGroup::getById($id);
    }

    public function getAllGroupsHtml()
    {
        $groups = SurveyGroup::getAll();
        $html = '<option value="">Choose</option>';
        foreach ($groups as $group) {
            $html .= '<option value="' . $group['id'] . '">' . $group['title_en'] . '</option>';
        }
        return $html;
    }
}
