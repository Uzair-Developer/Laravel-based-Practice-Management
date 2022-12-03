<?php
namespace core\user;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\BaseManager;
use core\clinic\ClinicRepository;
use core\enums\ResponseTypes;
use core\hospital\HospitalRepository;
use core\userLocalization\UserLocalizationRepository;
use Input;
use Reservation;
use UserGroup;

class UserManager extends BaseManager
{
    public $password = 12345678;

    function __construct()
    {
        $this->userValidator = new UserValidator();
    }

    public function login($inputs)
    {
        $remember_me = false;
        $validator = $this->userValidator->validateLogin($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        $loginInputs = array(
            'user_name' => $inputs['user_name'],
            'password' => $inputs['password'],
        );

        if (Input::get('remember_me') == 1) {
            $remember_me = true;
        }
        try {
            $user = Sentry::authenticate($loginInputs, $remember_me);
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, 'User was not found');

        } catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, 'User is not activated');

        } // The following is only required if the throttling is enabled
        catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, 'User is suspended');

        } catch (\Cartalyst\Sentry\Throttling\UserBannedException $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, 'User is banned');
        }
        try {
            Sentry::login($user, $remember_me);
        } catch (\Cartalyst\Sentry\Users\LoginRequiredException $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, 'Login field is required');

        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, 'User was not found');

        } catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, 'User is not activated');

        } // Following is only needed if throttle is enabled
        catch (\Cartalyst\Sentry\Throttling\UserBannedException $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, 'User is banned');
        }
        return $this->response()->ResponseObject(ResponseTypes::success, '', $user, false);

    }

    public function createUser($inputs)
    {
        $validator = $this->userValidator->validateUser($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            if (!empty($inputs['image_url'])) {
                $file = Input::file('image_url');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path() . '/uploads/users';
                $upload_success = $file->move($destinationPath, $filename);
                if ($upload_success) {
                    $inputs['image_url'] = 'uploads/users/' . $filename;
                }
            } else {
                unset($inputs['image_url']);
            }
            $inputData = array(
                'email' => $inputs['email'],
                'password' => $inputs['password'],
                'first_name' => $inputs['first_name'],
                'middle_name' => $inputs['middle_name'],
                'last_name' => $inputs['last_name'],
                'family_name' => $inputs['family_name'],
                'full_name' => $inputs['first_name'] . ' ' . $inputs['middle_name'] . ' ' . $inputs['last_name'] . ' ' . $inputs['family_name'],
                'user_name' => $inputs['user_name'],
                'first_name_ar' => $inputs['first_name_ar'],
                'last_name_ar' => $inputs['last_name_ar'],
                'user_type_id' => $inputs['user_type_id'],
                'phone_number' => isset($inputs['phone_number']) ? $inputs['phone_number'] : null,
                'mobile1' => $inputs['mobile1'],
                'mobile2' => isset($inputs['mobile2']) ? $inputs['mobile2'] : null,
                'address' => isset($inputs['address']) ? $inputs['address'] : '',
                'image_url' => isset($inputs['image_url']) ? $inputs['image_url'] : ''
            );
            if ($inputs['user_type_id'] == 7) {
                $inputData['user_experience_id'] = $inputs['user_experience_id'];
            }
            $user = Sentry::register($inputData, true);
            if (!empty($inputs['group_id'])) {
                foreach ($inputs['group_id'] as $key => $val) {
                    $group = Sentry::findGroupById($val);
                    $user->addGroup($group);
                }
            }
            if (!empty($inputs['clinic_ids'])) {
                $ULRepo = new UserLocalizationRepository();
                $clinicRepo = new ClinicRepository();
                $clinicIds = explode(',', $inputs['clinic_ids']);
                foreach ($clinicIds as $key => $val) {
                    $data = array(
                        'user_id' => $user->id,
                        'hospital_id' => $clinicRepo->getHospitalId($val),
                        'clinic_id' => $val
                    );
                    $ULRepo->save($data);
                }
            }
            if (!empty($inputs['hospital_ids'])) {
                $ULRepo = new UserLocalizationRepository();
                $hospitalIds = explode(',', $inputs['hospital_ids']);
                foreach ($hospitalIds as $key => $val) {
                    if (!$ULRepo->isHospitalExistForUser($user->id, $val)) {
                        $data = array(
                            'user_id' => $user->id,
                            'hospital_id' => $val,
                            'clinic_id' => ''
                        );
                        $ULRepo->save($data);
                    }
                }
            }
        } catch (\Cartalyst\Sentry\Users\LoginRequiredException $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, 'Login field is required');
        } catch (\Cartalyst\Sentry\Users\UserExistsException $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, 'User with this login already exists');
        }
        return $this->response()->ResponseObject(ResponseTypes::success, 'Added Successfully', $user);
    }

    public function updateUser($inputs, $id)
    {
        $validator = $this->userValidator->validateUser($inputs, true, $id);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $user = Sentry::getUserProvider()->findById($id);
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
            $user->full_name = $inputs['first_name'] . ' ' . $inputs['middle_name'] . ' ' . $inputs['last_name'] . ' ' . $inputs['family_name'];
            $user->first_name = $inputs['first_name'];
            $user->middle_name = $inputs['middle_name'];
            $user->last_name = $inputs['last_name'];
            $user->family_name = $inputs['family_name'];
            $user->first_name_ar = $inputs['first_name_ar'];
            $user->last_name_ar = $inputs['last_name_ar'];
            $user->user_name = $inputs['user_name'];
            $user->user_type_id = $inputs['user_type_id'];
            $user->phone_number = isset($inputs['phone_number']) ? $inputs['phone_number'] : '';
            $user->mobile1 = $inputs['mobile1'];
            $user->mobile2 = isset($inputs['mobile2']) ? $inputs['mobile2'] : '';
            $user->address = isset($inputs['address']) ? $inputs['address'] : '';
            if ($inputs['user_type_id'] == 7) {
                $user->user_experience_id = $inputs['user_experience_id'];
            } else {
                $user->user_experience_id = '';
            }
            if (isset($inputs['password']) && $inputs['password']) {
                $user->password = $inputs['password'];
            }
            $user->save();
            UserGroup::removeByUserId($id);
            if (!empty($inputs['group_id'])) {
                foreach ($inputs['group_id'] as $key => $val) {
                    $group = Sentry::findGroupById($val);
                    $user->addGroup($group);
                }
            }
            $ULRepo = new UserLocalizationRepository();
            $user_clinics = $ULRepo->getClinicsByUserId($id);
            $clinicIds = '';
            if (!empty($inputs['clinic_ids'])) {
                $clinicIds = explode(',', $inputs['clinic_ids']);
            }
            if (!empty($user_clinics)) {
                foreach ($user_clinics as $key => $val) {
                    if (!empty($clinicIds)) {
                        if (!in_array($val, $clinicIds)) {
                            $ULRepo->deleteWithUserAndClinic($id, $val);
                        } else {
                            $key = array_search($val, $clinicIds);
                            if ($key !== false) {
                                unset($clinicIds[$key]);
                            }
                        }
                    } else {
                        $ULRepo->deleteWithUserAndClinic($id, $val);
                    }
                }
            }
            if (!empty($clinicIds)) {
                $clinicRepo = new ClinicRepository();
                foreach ($clinicIds as $key => $val) {
                    $data = array(
                        'user_id' => $user->id,
                        'hospital_id' => $clinicRepo->getHospitalId($val),
                        'clinic_id' => $val
                    );
                    $ULRepo->save($data);
                }
            }
            $hospitalIds = '';
            if (!empty($inputs['hospital_ids'])) {
                $hospitalIds = explode(',', $inputs['hospital_ids']);
            }
            $user_hospitals = $ULRepo->getManageHospitalsByUserId($id);
            foreach ($user_hospitals as $key => $val) {
                if (!empty($hospitalIds)) {
                    if (!in_array($val, $hospitalIds)) {
                        $ULRepo->deleteWithUserAndHospital($id, $val);
                    }
                } else {
                    $ULRepo->deleteWithUserAndHospital($id, $val);
                }
            }
            if (!empty($hospitalIds)) {
                $ULRepo = new UserLocalizationRepository();
                foreach ($hospitalIds as $key => $val) {
                    if (!$ULRepo->isHospitalExistForUser($user->id, $val)) {
                        $data = array(
                            'user_id' => $id,
                            'hospital_id' => $val,
                            'clinic_id' => ''
                        );
                        $ULRepo->save($data);
                    }
                }
            }
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, 'User was not found');
        }
        return $this->response()->ResponseObject(ResponseTypes::success, 'Updated Successfully');
    }

    public function changeStatus($id)
    {
        try {
            $user = Sentry::findUserByID($id);
            if ($user->activated == 0) {
                $user->activated = 1;
            } else {
                $user->activated = 0;
                if ($user->user_type_id == 7) {
                    Reservation::cancelAllReservationOfDoctor($id, date('Y-m-d'));
                }
            }
            $user->save();

        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, $e->getMessage());
        }
        return $this->response()->ResponseObject(ResponseTypes::success, 'Updated Successfully');
    }

}