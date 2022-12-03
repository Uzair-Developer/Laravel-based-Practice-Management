<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\enums\AttributeType;
use core\enums\UserRules;
use core\hospital\HospitalRepository;
use core\physician\PhysicianManager;
use core\physician\PhysicianRepository;
use core\user\UserManager;
use core\user\UserRepository;
use core\user\UserValidator;
use core\userLocalization\UserLocalizationRepository;

class UserController extends BaseController
{
    public $user, $userManager, $userRepo = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login', array('except' => array('loginForm', 'login')));
        $this->userManager = new UserManager();
        $this->userRepo = new UserRepository();
        $this->user = Sentry::getUser();
    }

    public function index()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('user.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $data['users'] = $this->userRepo->getAllPagination($inputs);
        $data['links'] = $data['users']->appends(Input::except('_token'))->links();

        $data['groups'] = Group::getAll();
        $data['userTypes'] = UserTypes::getAll();
        return View::make('user/list', $data);
    }

    public function addUser()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('user.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['groups'] = Group::getAll();
        $data['userTypes'] = UserTypes::getAll();
        $data['experience'] = AttributePms::getAll(AttributeType::$pmsReturn['userExperience']);
        if ($this->user->user_type_id == 2) {
            unset($data['userTypes'][0]);
        }
        $data['hospitals'] = $hospitalRepo->getAllWithClinics();
        return View::make('user/add', $data);
    }

    public function createUser()
    {
        $inputs = Input::except('_token');
        unset($inputs['_token']);
        $user = $this->userManager->createUser($inputs);
        if ($user['status']) {
            return Redirect::route('users');
        } else {
            return Redirect::back()->withInput();
        }
    }

    public function editUser($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('user.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['user'] = $this->userRepo->getById($id);
        $data['groups'] = Group::getAll();
        $data['user_groups'] = UserGroup::getGroupByUserId($id);
        $data['userTypes'] = UserTypes::getAll();
        $data['experience'] = AttributePms::getAll(AttributeType::$pmsReturn['userExperience']);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAllWithClinics();

        $ULRepo = new UserLocalizationRepository();
        $data['user_clinics'] = $ULRepo->getClinicsByUserId($data['user']['id']);
        foreach ($data['user_clinics'] as $key => $val) {
            if ($val == 0) {
                unset($data['user_clinics'][$key]);
            }
        }
        $data['user_hospitals'] = $ULRepo->getManageHospitalsByUserId($data['user']['id']);
        return View::make('user/edit', $data);
    }

    public function updateUser($id)
    {
        $inputs = Input::except('_token');
        $user = $this->userManager->updateUser($inputs, $id);
        if ($user['status']) {
            return Redirect::route('users');
        } else {
            return Redirect::back()->withInput();
        }
    }

    public function editProfile()
    {
        $user_id = $this->user->id;
        $data['user'] = $this->userRepo->getById($user_id);
        if($this->user->user_type_id == UserRules::physician){
            $physicianRepo = new PhysicianRepository();
            $data['physician'] = $physicianRepo->getByUserId($user_id);
//            $data['specialty'] = AttributePms::getAll(AttributeType::$pmsReturn['specialty']);
            $data['experience'] = AttributePms::getAll(AttributeType::$pmsReturn['userExperience']);
            $data['countries'] = Country::getParents();
            $data['form_action'] = route('updatePhysicianProfile');

            $userLocal = new UserLocalizationRepository();
            $clinicIds = $userLocal->getClinicsByUserId($user_id);
            $data['clinic_services'] = PhysicianAttribute::getAll(array('type' => 1, 'clinic_ids' => $clinicIds));
            $data['performed_operations'] = PhysicianAttribute::getAll(array('type' => 2, 'clinic_ids' => $clinicIds));
            $data['equipments'] = PhysicianAttribute::getAll(array('type' => 3, 'clinic_ids' => $clinicIds));
            $data['specialty'] = PhysicianAttribute::getAll(array('type' => 4, 'clinic_ids' => $clinicIds));
            return View::make('physician/edit', $data);
        } else {
            return View::make('user/profile', $data);
        }
    }

    public function updatePhysicianProfile()
    {
        $id = $this->user->id;
        $systemManager = new PhysicianManager();
        $inputs = (Input::except('_token'));
        $data = $systemManager->updatePhysician($inputs, $id);
        if ($data['status']) {
            return Redirect::back();
        } else {
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function updateProfile()
    {
        $user_id = $this->user->id;
        $userValidator = new UserValidator();
        $inputs = Input::except('_token');
        $validator = $userValidator->validateUser($inputs, true, $user_id);
        if ($validator->fails()) {
            Flash::error($validator->messages());
        }
        try {
            $user = Sentry::getUserProvider()->findById($user_id);
            if (!empty($inputs['image_url'])) {
                $file = Input::file('image_url');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path() . '/uploads/users';
                $upload_success = $file->move($destinationPath, $filename);
                if ($upload_success) {
                    $user->image_url = 'uploads/users/' . $filename;
                }
            }
            $user->email = $inputs['email'];
            $user->full_name = $inputs['full_name'];
            $user->phone_number = isset($inputs['phone_number']) ? $inputs['phone_number'] : '';
            $user->mobile1 = $inputs['mobile1'];
            $user->mobile2 = isset($inputs['mobile2']) ? $inputs['mobile2'] : '';
            $user->address = isset($inputs['address']) ? $inputs['address'] : '';
            if ($inputs['password']) {
                $user->password = $inputs['password'];
            }
            $user->save();
        } catch (\Exception $e) {
            Flash::error('Ops, try again later!');
        }
        Flash::success('Updated Successfully');
        return Redirect::back();
    }

    public function deleteUser($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('user.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $this->userRepo->delete($id);
        Flash::success('Delete Successfully');
        return Redirect::route('users');
    }

    public function changeStatus($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('user.changeStatus') && !$this->user->hasAccess('physician.changeStatus')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $this->userManager->changeStatus($id);
        return Redirect::back();
    }

    public function loginForm()
    {
        return View::make('user/login');
    }

    public function login()
    {
        $systemRun = User::where('id', 1)->first();
        if($systemRun['system_run'] != 1) {
            die('');
        }
        $inputs = Input::except('_token');
        $userManager = new UserManager();
        $user = $userManager->login($inputs);
        if ($user['status']) {
            $userData = Sentry::getUser();
            if ($userData['change_password'] == 1) {
                return View::make('user/must_change_pass', array('user_id' => $userData['id']));
            }
            if ($user['return']['user_type_id'] == UserRules::physician) {
                User::edit(array(
                    'is_ready' => 1,
                ), $user['return']['id']);
                Monitor::add(array(
                    'user_id' => $user['return']['id'],
                    'status' => 1,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s')
                ));
                $physicianData = Physician::getByPhysicianId($user['return']['id']);
                if(isset($physicianData['current_status']) && $physicianData['current_status'] == 0){
                    Session::flash('edit_profile', 'yes');
                }
                if($physicianData){
                    Session::flash('edit_profile', 'yes');
                }
            }
            return Redirect::route('home');
        } else {
            return Redirect::back();
        }
    }

    public function logout()
    {
        Sentry::logout();
        if ($this->user->user_type_id == UserRules::physician) {
            Monitor::add(array(
                'user_id' => $this->user->id,
                'status' => 0,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s')
            ));
            User::edit(array(
                'is_ready' => 0,
            ), $this->user->id);

        }
        return Redirect::route('loginForm');
    }

    public function mustChangePassword()
    {
        $rules = array(
            'user_id' => "required",
            "password" => "required|confirmed"
        );
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back();
        } else {
            try {
                $user = Sentry::getUserProvider()->findById($inputs['user_id']);
                $user->password = $inputs['password'];
                $user->change_password = 2;
                $user->save();
                Flash::success('Update Successfully');
                return Redirect::route('home');
            } catch (\Exception $e) {
                Flash::error('Ops, Their is error');
                return Redirect::back();
            }
        }
    }

    public function resetPassword($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('user.resetPassword')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $userManager = new UserManager();
        try {
            $user = Sentry::getUserProvider()->findById($id);
            $user->password = $userManager->password;
            $user->save();
        } catch (\Exception $e) {
            Flash::error('Ops, Their is error');
            return Redirect::back();
        }
        Flash::success('Updated Successfully');
        return Redirect::back();
    }

    public function changePassword()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('user.changePassword')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $rules = array(
            'user_id' => "required",
            "password" => "required|confirmed"
        );
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back();
        } else {
            try {
                $user = Sentry::getUserProvider()->findById($inputs['user_id']);
                $user->password = $inputs['password'];
                $user->save();
                Flash::success('Update Successfully');
                return Redirect::back();
            } catch (\Exception $e) {
                Flash::error('Ops, Their is error');
                return Redirect::back();
            }
        }
    }

    public function printExcelUsers()
    {
        $inputs = Input::except('_token');
        $users = $this->userRepo->getAllPagination($inputs, false);
        Excel::create('users_' . date('Y-m-d H-i-s'), function ($excel) use ($users) {
            // Set the title
            $excel->setTitle('users');
            $excel->sheet('users', function ($sheet) use ($users) {
                $sheet->loadView('user/printExcel', array('users' => $users));
            });

        })->download('xlsx');
    }
}