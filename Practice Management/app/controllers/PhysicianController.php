<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\enums\AttributeType;
use core\enums\UserRules;
use core\hospital\HospitalRepository;
use core\physician\PhysicianManager;
use core\physician\PhysicianRepository;
use core\user\UserRepository;
use core\userLocalization\UserLocalizationRepository;

class PhysicianController extends BaseController
{
    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function index()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physician.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $physicianRepo = new PhysicianRepository();
        $physicians = $physicianRepo->getAll($inputs);
        foreach ($physicians as $key => $val) {
            $data = Physician::getByPhysicianId($val['id']);
            if ($data) {
                $physicians[$key]['physicianData'] = $data->toArray();
            } else {
                $physicians[$key]['physicianData'] = '';
            }
        }
        $data['physicians'] = $physicians;
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('physician/list', $data);
    }

    public function editPhysician($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physician.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $physicianRepo = new PhysicianRepository();
        $userRepo = new UserRepository();
        $data['user'] = $userRepo->getById($id);
        $data['physician'] = $physicianRepo->getByUserId($id);
//        $data['specialty'] = AttributePms::getAll(AttributeType::$pmsReturn['specialty']);
        $data['experience'] = AttributePms::getAll(AttributeType::$pmsReturn['userExperience']);

        $data['countries'] = Country::getParents();

        $userLocal = new UserLocalizationRepository();
        $clinicIds = $userLocal->getClinicsByUserId($id);
        $data['clinic_services'] = PhysicianAttribute::getAll(array('type' => 1, 'clinic_ids' => $clinicIds));
        $data['performed_operations'] = PhysicianAttribute::getAll(array('type' => 2, 'clinic_ids' => $clinicIds));
        $data['equipments'] = PhysicianAttribute::getAll(array('type' => 3, 'clinic_ids' => $clinicIds));
        $data['specialty'] = PhysicianAttribute::getAll(array('type' => 4, 'clinic_ids' => $clinicIds));

        $data['form_action'] = route('updatePhysician', array($id));
        return View::make('physician/edit', $data);
    }

    public function updatePhysician($id)
    {
        $systemManager = new PhysicianManager();
        $inputs = (Input::except('_token'));
        $data = $systemManager->updatePhysician($inputs, $id);
        if ($data['status']) {
            return Redirect::back();
        } else {
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function getPhysicianByClinicId()
    {
        $ULRepo = new UserLocalizationRepository();
        $clinic_id = Input::get('clinic_id');
        $user_experience_id = Input::get('user_experience_id');
        $user_specialty_id = Input::get('user_specialty_id');
        $bookable = '';
        if (Input::has('bookable')) {
            $bookable = Input::get('bookable');
        }
        if ($this->user->user_type_id == UserRules::physician) {
            if ($ULRepo->isClinicExistForUser($this->user->id, $clinic_id)) {
                $html = '<option value="">Choose</option>';
                $physician = User::getById($this->user->id);
                if (Input::has('in_report')) {
                    $in_report = Input::get('in_report');
                    if ($in_report == 1) {
                        if ($physician['in_report'] != 1) {
                            return $html;
                        }
                    }
                }
                $html .= '<option value="' . $physician['id'] . '">' . $physician['full_name'] . '</option>';
                return $html;
            } else {
                return '';
            }
        } else {
            $physiciansIds = User::getPhysicianByClinicId($clinic_id);
            $physicians = User::getByIds($physiciansIds, $user_experience_id, $user_specialty_id, $bookable);
            $html = '<option value="">Choose</option>';
            foreach ($physicians as $key => $val) {
                if (Input::has('in_report')) {
                    $in_report = Input::get('in_report');
                    if ($in_report == 1) {
                        if ($val['in_report'] != 1) {
                            continue;
                        }
                    }
                }
                $html .= '<option value="' . $val['id'] . '">' . $val['full_name'] . '</option>';
            }
            return $html;
        }
    }

    public function getPhysicianByClinicIds()
    {
        $clinic_id = Input::get('clinic_id');
        $physiciansIds = User::getPhysicianByClinicIds($clinic_id);
        $physicians = User::getByIds($physiciansIds);
        $html = '';
        foreach ($physicians as $key => $val) {
            $html .= '<option value="' . $val['id'] . '">' . $val['full_name'] . '</option>';
        }
        return $html;
    }

    public function getAnyClinicAndHospitalByPhysician()
    {
        $physicianId = Input::get('physician_id');
        $clinicObj = new UserLocalizationRepository();
        $clinic = $clinicObj->getFirstClinicByUserId($physicianId);
        $data['clinic_id'] = $clinic->clinic_id;
        $data['hospital_id'] = Clinic::getById($clinic->clinic_id)['hospital_id'];
        return $data;
    }

    public function getPhysicianProfile()
    {
        $inputs = Input::except('_token');
        $data['physician'] = User::getById($inputs['physician_id']);
        $data['physician']['profile'] = Physician::getByPhysicianId($inputs['physician_id']);
        return $data;
    }

    public function changeProfileStatus($id, $status)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('head_dept.access')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $physician = Physician::getByPhysicianId($id);
        if ($status == 'publish') {
            Physician::edit(array(
                'current_status' => 2, // publish
                'previous_status' => $physician['current_status'],
            ), $physician['id']);
            Flash::success('Doctor\'s profile is published now!');
        } elseif ($status == 'back-to-dr') {
            Physician::edit(array(
                'current_status' => 0, // no action (doctor can edit his profile)
                'previous_status' => $physician['current_status'],
                'head_notes' => $inputs['head_notes'],
            ), $physician['id']);
            Flash::success('Updated Successfully');
        } elseif ($status == 'un-publish') {
            Physician::edit(array(
                'current_status' => 0, // no action (doctor can edit his profile)
                'previous_status' => $physician['current_status'],
                'head_notes' => $inputs['head_notes'],
            ), $physician['id']);
            Flash::success('Doctor\'s profile is un published now!');
        }
        return Redirect::back();
    }

    public function hisImportPhysician()
    {
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();

        $inputs = Input::except('_token');
        $data['inputs'] = $inputs;

        if (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
            if ($inputs['hospital_id'] == 2) {
                $physicianIds = User::getPhysiciansId(true, true, 'riyadh');
                $data['his_physicians'] = RiyadhPhysician::getAllExceptIds($physicianIds);
            } elseif ($inputs['hospital_id'] == 1) {
                $physicianIds = User::getPhysiciansId(true, true);
                $data['his_physicians'] = HisPhysician::getAllExceptIds($physicianIds);
            }
            $data['deactivated_physicians'] = User::getDeactivatedPhysicians($inputs['hospital_id']);
        }
        return View::make('physician/his_import', $data);
    }

    public function getPhysiciansFromHIS()
    {
        $inputs = Input::except('_token');
        if (isset($inputs['physician_ids']) && $inputs['physician_ids'] && isset($inputs['hospital_id']) && $inputs['hospital_id']) {
            $physician_ids = explode(',', $inputs['physician_ids']);
            if($inputs['hospital_id'] == 2) {
                $physicians = RiyadhPhysician::getByIds($physician_ids);
            } else {
                $physicians = HisPhysician::getByIds($physician_ids);
            }
            $userArray = array();
            foreach ($physicians as $key => $val) {
                $full_name = $val['FullName_EN'];
                $full_name_parts = explode(' ', $full_name);

                $username = strtolower($full_name_parts[0]) . '.' . strtolower(end($full_name_parts));
                if (User::checkUsernameExist($username)) {
                    $username = $username . rand(000, 999);
                }
                $inputData = array(
                    'password' => 12345678,
                    'full_name' => $full_name,
                    'first_name' => isset($full_name_parts[0]) ? $full_name_parts[0] : '',
                    'middle_name' => isset($full_name_parts[1]) ? $full_name_parts[1] : '',
                    'last_name' => isset($full_name_parts[2]) ? $full_name_parts[2] : '',
                    'family_name' => isset($full_name_parts[3]) ? $full_name_parts[3] : '',
                    'user_name' => $username,
                    'his_id' => $val['HIS_Id'],
                    'user_type_id' => 7,
                    'insert_type' => 2
                );
                if($inputs['hospital_id'] == 2) {
                    $inputData['his_id'] = '';
                    $inputData['his_id_2'] = $val['HIS_Id'];
                }
                $user = Sentry::register($inputData, true);
                $userArray[] = $user->id;

                $inputData2 = array(
                    'user_id' => $user->id,
                    'his_id' => $val['HIS_Id'],
                    'department_name' => $val['DepartmentName'],
                    'department_id' => $val['Department_Id'],
                );
                if($inputs['hospital_id'] == 2) {
                    $inputData2['his_id'] = '';
                    $inputData2['his_id_2'] = $val['HIS_Id'];
                }
                Physician::add($inputData2);

                UserGroup::add(array(
                    'user_id' => $user->id,
                    'group_id' => 8,
                ));

                if($inputs['hospital_id'] == 2) {
                    $checkClinic = Clinic::getByHisId($val['Department_Id']);
                } else {
                    $checkClinic = Clinic::getById($val['Department_Id']);
                }
                if ($checkClinic) {
                    if($inputs['hospital_id'] == 2) {
                        Clinic::editByHisId(array(
                            'code' => $val['DepartmentCode']
                        ), $val['Department_Id']);
                    } else {
                        Clinic::edit(array(
                            'code' => $val['DepartmentCode']
                        ), $val['Department_Id']);
                    }
                } else {
                    $addClinic = array(
                        'name' => $val['DepartmentName'],
                        'code' => $val['DepartmentCode'],
                    );
                    if($inputs['hospital_id'] == 2) {
                        $addClinic['his_id'] = $val['Department_Id'];
                        $addClinic['hospital_id'] = 2;
                    } else {
                        $addClinic['id'] = $val['Department_Id'];
                        $addClinic['hospital_id'] = 1;
                    }
                    $checkClinic = Clinic::add($addClinic);
                }

                $addLocal = array(
                    'user_id' => $user->id,
                    'hospital_id' => 1,
                    'clinic_id' => $checkClinic['id'],
                );
                if($inputs['hospital_id'] == 2) {
                    $addLocal['hospital_id'] = 2;
                }
                UserLocalization::add($addLocal);

            }
            $data['insertedPhysicians'] = User::getByIds($userArray);
            $data['formAction'] = route('updateArabicNamePhysicians');
            $data2['modal_html'] = View::make('physician/add_physician_arabic', $data)->render();
            $data2['success'] = 'yes';
            $data2['message'] = 'Successfully Added, Plz Add The Arabic Name Of Physicians!';

        } else {
            $data2['modal_html'] = '';
            $data2['success'] = 'no';
            $data2['message'] = 'Ops, try again later!';
        }
        return $data2;
    }

    public function updateArabicNamePhysicians()
    {
        $inputs = Input::except('_token');
        foreach ($inputs['id'] as $key => $val) {
            User::edit(array(
                'first_name_ar' => $inputs['first_name_ar'][$key],
                'last_name_ar' => $inputs['last_name_ar'][$key],
                'bookable' => $inputs['bookable'][$key],
                'in_report' => $inputs['in_report'][$key],
                'revisit_limit' => $inputs['revisit_limit'][$key] ? $inputs['revisit_limit'][$key] : 10,
            ), $val);
        }
        Flash::success('Updated Successfully');
        return Redirect::route('hisImportPhysician');

    }

    public function getActivatePhysicianFromHISForm()
    {
        $inputs = Input::except('_token');
        if ($inputs['physician_id']) {
            $physician_id = explode(',', $inputs['physician_id']);
            $data['insertedPhysicians'] = User::getByIds($physician_id, null, null, false, false);
            $data['activateAction'] = true;
            $data['formAction'] = route('postActivatePhysicianFromHISForm');
            $data2['modal_html'] = View::make('physician/add_physician_arabic', $data)->render();
            $data2['success'] = 'yes';
            $data2['message'] = 'Successfully Added, Plz Add The Arabic Name Of Physician!';
        } else {
            $data2['success'] = 'no';
            $data2['message'] = 'Missing Data!';
        }
        return $data2;
    }

    public function postActivatePhysicianFromHISForm()
    {
        $inputs = Input::except('_token');
        User::edit(array(
            'first_name_ar' => $inputs['first_name_ar'],
            'last_name_ar' => $inputs['last_name_ar'],
            'activated' => 1,
            'bookable' => $inputs['bookable'],
            'in_report' => $inputs['in_report'],
            'revisit_limit' => $inputs['revisit_limit'] ? $inputs['revisit_limit'] : 10,
        ), $inputs['id']);

        Flash::success('Updated Successfully');
        return Redirect::route('hisImportPhysician');
    }

    public function uploadPhysicianImage()
    {
        $inputs = Input::except('_token');
        if (!empty($inputs['file'])) {
            $file = Input::file('file');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path() . '/uploads/physician/images';
            $upload_success = $file->move($destinationPath, $filename);
            if ($upload_success) {
                $inputs['image_url'] = 'uploads/physician/images/' . $filename;
            }
        } else {
            unset($inputs['image_url']);
        }
        User::edit(array(
            'image_url' => isset($inputs['image_url']) ? $inputs['image_url'] : '',
        ), $inputs['physician_id']);
        return $inputs['image_url'];
    }

    public function saveTap0()
    {
        $inputs = Input::except('_token');
        $user_id = $inputs['user_id'];
        $user = Sentry::getUserProvider()->findById($user_id);
        $inputs = $inputs['tab_0'];
        if ($inputs['password']) {
            if ($inputs['password_confirmation']) {
                if ($inputs['password'] == $inputs['password_confirmation']) {
                    $user->password = $inputs['password'];
                } else {
                    $data['success'] = 'no';
                    $data['message'] = 'Password Confirmation must equal Password field!';
                    return $data;
                }
            } else {
                $data['success'] = 'no';
                $data['message'] = 'Password Confirmation is required!';
                return $data;
            }
        }
        $user->extension_num = $inputs['extension_num'];
        $user->email = $inputs['email'];
        $user->full_name = $inputs['first_name'] . ' ' . $inputs['middle_name'] . ' ' . $inputs['last_name'] . ' ' . $inputs['family_name'];
        $user->first_name = $inputs['first_name'];
        $user->middle_name = $inputs['middle_name'];
        $user->last_name = $inputs['last_name'];
        $user->family_name = $inputs['family_name'];
        $user->first_name_ar = $inputs['first_name_ar'];
        $user->last_name_ar = $inputs['last_name_ar'];
        $user->phone_number = isset($inputs['phone_number']) ? $inputs['phone_number'] : '';
        $user->mobile1 = $inputs['mobile1'];
        $user->mobile2 = isset($inputs['mobile2']) ? $inputs['mobile2'] : '';
        $user->address = isset($inputs['address']) ? $inputs['address'] : '';
        if ($this->user->user_type_id == 1 || $this->user->user_type_id != 7) {
            $user->bookable = $inputs['bookable'] == 1 ? 1 : 2;
            $user->in_report = isset($inputs['in_report']) ? 1 : 2;
            $user->revisit_limit = $inputs['revisit_limit'] ? $inputs['revisit_limit'] : 0;
        }
        $user->save();
        unset($inputs['save_status']);
        unset($inputs['password']);
        unset($inputs['password_confirmation']);
        unset($inputs['image_url']);
        unset($inputs['first_name']);
        unset($inputs['middle_name']);
        unset($inputs['last_name']);
        unset($inputs['family_name']);
        unset($inputs['first_name_ar']);
        unset($inputs['last_name_ar']);
        unset($inputs['extension_num']);
        unset($inputs['email']);
        unset($inputs['phone_number']);
        unset($inputs['mobile1']);
        unset($inputs['mobile2']);
        unset($inputs['address']);
        unset($inputs['bookable']);
        unset($inputs['in_report']);
        unset($inputs['revisit_limit']);

        $inputs['country_id2'] = isset($inputs['country_id']) ? $inputs['country_id'] : '';
        $inputs['city_id2'] = isset($inputs['city_id']) ? $inputs['city_id'] : '';
        $inputs['birthdate2'] = isset($inputs['birthdate']) ? $inputs['birthdate'] : '';
        $inputs['gender2'] = isset($inputs['gender']) ? $inputs['gender'] : '';
        $inputs['user_id'] = $user_id;

        $Repo = new PhysicianRepository();
        $physicianData = $Repo->getByUserId($user_id);
        if ($physicianData) {
            $Repo->update($inputs, $user_id);
        } else {
            $Repo->save($inputs);
        }
        $data['success'] = 'yes';
        $data['message'] = 'Updated Successfully!';
        return $data;
    }

    public function saveTap1()
    {
        $inputs = Input::except('_token');
        parse_str($inputs['tab_1'], $tab_1);
        $user_id = $inputs['user_id'];
        $user = Sentry::getUserProvider()->findById($user_id);
        $inputs = $tab_1;
        $user->user_specialty_id = isset($inputs['specialty_id']) ? implode(',', $inputs['specialty_id']) : null;
        $user->user_experience_id = isset($inputs['user_experience_id']) ? $inputs['user_experience_id'] : null;
        $user->save();

        unset($inputs['specialty_id']);
        unset($inputs['user_experience_id']);

        $inputs['graduation2'] = isset($inputs['graduation']) ? $inputs['graduation'] : '';
        $inputs['graduated_from2'] = isset($inputs['graduated_from']) ? $inputs['graduated_from'] : '';
        $inputs['degree2'] = isset($inputs['degree']) ? $inputs['degree'] : '';
        $inputs['job_position2'] = isset($inputs['job_position']) ? $inputs['job_position'] : '';
        $inputs['about2'] = isset($inputs['about']) ? $inputs['about'] : '';
        $inputs['attaches2'] = isset($inputs['attaches']) ? $inputs['attaches'] : '';
        $inputs['license_number2'] = isset($inputs['license_number']) ? $inputs['license_number'] : '';
        $inputs['license_activation2'] = isset($inputs['license_activation']) ? $inputs['license_activation'] : '';
        $inputs['certificates2'] = isset($inputs['certificates']) ? $inputs['certificates'] : '';
        $inputs['awards2'] = isset($inputs['awards']) ? $inputs['awards'] : '';
        $inputs['credentials2'] = isset($inputs['credentials']) ? $inputs['credentials'] : '';
        $inputs['user_id'] = $user_id;

        $Repo = new PhysicianRepository();
        $physicianData = $Repo->getByUserId($user_id);
        if ($physicianData) {
            $Repo->update($inputs, $user_id);
        } else {
            $Repo->save($inputs);
        }
        $data['success'] = 'yes';
        $data['message'] = 'Updated Successfully!';
        return $data;
    }

    public function saveTap2()
    {
        $inputs = Input::except('_token');
        $user_id = $inputs['user_id'];
        parse_str($inputs['tab_2'], $tab_2);
        $inputs = $tab_2;
        if (isset($inputs['clinic_services'])) {
            $inputs['clinic_services'] = implode(',', $inputs['clinic_services']);
        }
        if (isset($inputs['performed_operations'])) {
            $inputs['performed_operations'] = implode(',', $inputs['performed_operations']);
        }
        if (isset($inputs['equipments'])) {
            $inputs['equipments'] = implode(',', $inputs['equipments']);
        }

        $inputs['equipments2'] = isset($inputs['equipments']) ? $inputs['equipments'] : '';
        $inputs['clinic_services2'] = isset($inputs['clinic_services']) ? $inputs['clinic_services'] : '';
        $inputs['performed_operations2'] = isset($inputs['performed_operations']) ? $inputs['performed_operations'] : '';
        $inputs['notes2'] = isset($inputs['notes']) ? $inputs['notes'] : '';
        $inputs['user_id'] = $user_id;

        $Repo = new PhysicianRepository();
        $physicianData = $Repo->getByUserId($user_id);
        if ($physicianData) {
            $Repo->update($inputs, $user_id);
        } else {
            $Repo->save($inputs);
        }
        $data['success'] = 'yes';
        $data['message'] = 'Updated Successfully!';
        return $data;
    }
}
