<?php

class QuestionController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listQuestion()
    {
        if (!$this->user->hasAccess('question.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $inputs['paginate'] = 20;
        $data['questions'] = Question::getAll($inputs);
        $data['answer_types'] = AnswerType::getAll();
        return View::make('questions/list', $data);
    }

    public function createQuestion()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, Question::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                Question::add($inputs);
                Flash::success('Added successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::back();
    }

    public function updateQuestion()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, Question::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                $id = $inputs['id'];
                Question::edit($inputs, $id);
                Flash::success('Updated successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::back();
    }

    public function deleteQuestion($id)
    {
        if (!$this->user->hasAccess('question.delete') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        Question::remove($id);
        Flash::success('Deleted successfully');
        return Redirect::route('listQuestion');
    }

    public function getQuestion()
    {
        $id = Input::get('id');
        return Question::getById($id);
    }

    public function getQuestionBySurvey()
    {
        $survey_id = Input::get('survey_id');
        $questionIds = GroupQuestion::getAll(['survey_id' => $survey_id, 'getIds' => 'question_id']);
        $questions = Question::getAll(['ids' => $questionIds]);
        $html = '<option value="">Choose</option>';
        foreach ($questions as $val) {
            $html .= '<option value="' . $val['id'] . '">' . $val['title_en'] . '</option>';
        }
        return $html;
    }

    public function getAnswerByQuestion()
    {
        $inputs = Input::except('_token');
        $question = Question::getById($inputs['question_id']);
        $answer = AnswerType::getById($question['answer_type']);
        $answers = explode(',', $answer['answers_en']);
        $html = '<option value="">Choose</option>';
        foreach ($answers as $key => $val) {
            $html .= '<option value="' . $key . '">' . $val . '</option>';
        }
        return $html;
    }
}
