<?php

class SurveyController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listSurvey()
    {
        if (!$this->user->hasAccess('survey.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $inputs['paginate'] = 20;
        $data['surveys'] = Survey::getAll($inputs);
        return View::make('surveys/list', $data);
    }

    public function addSurvey()
    {
        if (!$this->user->hasAccess('survey.add') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }

        $data['groups'] = SurveyGroup::getAll();
        $data['questions'] = Question::getAll();
        $data['survey'] = [
            'id' => '',
            'header_en' => '',
            'header_ar' => '',
            'footer_en' => '',
            'footer_ar' => '',
            'description_en' => '',
            'description_ar' => ''
        ];

        return View::make('surveys/add', $data);
    }

    public function createSurvey()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, Survey::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                $existing_groups = $inputs['ex_group'];
                unset($inputs['ex_group']);

                $insert_survey = Survey::add($inputs);
                if(isset($existing_groups['group_id']) && !empty($existing_groups['group_id'])) {
                    foreach ($existing_groups['group_id'] as $k => $val) {
                        $checkExist = GroupQuestion::getAll(['group_id' => $val, 'question_id' => $existing_groups['question_id'][$k], 'survey_id' => $insert_survey->id, 'getFirst'=>true]);
                        if(!$checkExist) {
                            GroupQuestion::add([
                                'group_id' => $val,
                                'question_id' => $existing_groups['question_id'][$k],
                                'survey_id' => $insert_survey->id
                            ]);
                        }
                    }
                    foreach ($existing_groups['group_id'] as $k => $val) {
                        $checkExist = GroupToSurvey::getAll(['group_id' => $val, 'survey_id' => $insert_survey->id, 'getFirst' => true]);
                        if(!$checkExist) {
                            GroupToSurvey::add([
                                'group_id' => $val,
                                'survey_id' => $insert_survey->id
                            ]);
                        }
                    }
                }
                Flash::success('Added successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::route('listSurvey');
    }

    public function editSurvey($id)
    {
        if (!$this->user->hasAccess('survey.edit') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }

        $data['groups'] = SurveyGroup::getAll();
        $data['questions'] = Question::getAll();
        $data['survey'] = Survey::getById($id);
        $groupToSurveys = GroupToSurvey::getAll(['survey_id' => $id]);
        $arr = [];
        foreach($groupToSurveys as $group){
            $questions = GroupQuestion::getAll(['group_id' => $group['group_id'] , 'survey_id' => $id]);
            foreach($questions as $question) {
                $arr[] = ["group_id" => $group['group_id'], 'question_id' => $question['question_id']];
            }
        }

        $data['selectedGroupsAndQuestions'] = $arr;

        return View::make('surveys/add', $data);
    }

    public function updateSurvey($id)
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, Survey::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                $existing_groups = $inputs['ex_group'];
                unset($inputs['ex_group']);

                Survey::edit($inputs, $id);

                if(isset($existing_groups['group_id']) && !empty($existing_groups['group_id'])) {
                    // remove all group question and group to survey then re-insert them
                    GroupQuestion::removeBySurvey($id);
                    GroupToSurvey::removeBySurvey($id);

                    foreach ($existing_groups['group_id'] as $k => $val) {
                        $checkExist = GroupQuestion::getAll(['group_id' => $val, 'question_id' => $existing_groups['question_id'][$k], 'survey_id' => $id, 'getFirst'=>true]);
                        if(!$checkExist) {
                            GroupQuestion::add([
                                'group_id' => $val,
                                'question_id' => $existing_groups['question_id'][$k],
                                'survey_id' => $id
                            ]);
                        }
                    }
                    foreach ($existing_groups['group_id'] as $k => $val) {
                        $checkExist = GroupToSurvey::getAll(['group_id' => $val, 'survey_id' => $id, 'getFirst' => true]);
                        if(!$checkExist) {
                            GroupToSurvey::add([
                                'group_id' => $val,
                                'survey_id' => $id
                            ]);
                        }
                    }
                }
                Flash::success('updated successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::route('listSurvey');
    }

    public function deleteSurvey($id)
    {
        if (!$this->user->hasAccess('survey.delete') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        Survey::remove($id);
        Flash::success('Deleted successfully');
        return Redirect::route('listSurvey');
    }

    public function getSurvey()
    {
        $id = Input::get('id');
        return Survey::getById($id);
    }
}
