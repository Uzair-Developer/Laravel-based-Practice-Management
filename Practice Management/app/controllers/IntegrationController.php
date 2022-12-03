<?php

class IntegrationController extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function getHisPhysicians()
    {
        $physicians = HisPhysician::getAll();
        foreach ($physicians as $key => $val) {
            $physicianData = Physician::checkHisExist($val['HIS_Id']);

            $full_name = $val['FullName_EN'];
            $full_name_parts = explode(' ', $full_name);

            $username = strtolower($full_name_parts[0]) . '.' . strtolower(end($full_name_parts));
            if (User::checkUsernameExist($username)) {
                $username = $username . rand(000, 999);
            }
            if (!$physicianData) {
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
                $user = Sentry::register($inputData, true);
                Physician::add(array(
                    'user_id' => $user->id,
                    'his_id' => $val['HIS_Id'],
                    'department_name' => $val['DepartmentName'],
                    'department_id' => $val['Department_Id'],
                ));

                UserGroup::add(array(
                    'user_id' => $user->id,
                    'group_id' => 8,
                ));

                UserLocalization::add(array(
                    'user_id' => $user->id,
                    'hospital_id' => 1,
                    'clinic_id' => $val['Department_Id'],
                ));

                if (Clinic::getById($val['Department_Id'])) {
                    Clinic::edit(array(
                        'code' => $val['DepartmentCode']
                    ), $val['Department_Id']);
                } else {
                    Clinic::add(array(
                        'id' => $val['Department_Id'],
                        'name' => $val['DepartmentName'],
                        'code' => $val['DepartmentCode'],
                        'hospital_id' => 1,
                    ));
                }

                HisPhysician::edit(array(
                    'MERGE_FLAG' => 0
                ), $val['Id']);
            } else {
                User::edit(array(
                    'full_name' => $full_name,
                    'first_name' => isset($full_name_parts[0]) ? $full_name_parts[0] : '',
                    'middle_name' => isset($full_name_parts[1]) ? $full_name_parts[1] : '',
                    'last_name' => isset($full_name_parts[2]) ? $full_name_parts[2] : '',
                    'family_name' => isset($full_name_parts[3]) ? $full_name_parts[3] : '',
//                    'user_name' => $username,
                ), $physicianData['user_id']);
            }
        }
    }

    public function getHisPatient()
    {
        ini_set('max_execution_time', 0);
        HISPatient::getAll();
    }

    public function getRydHisPatient()
    {
        ini_set('max_execution_time', 0);
        RiyadhPatient::getAll();
    }

    public function getPatientLabRadiology()
    {
        $orderIds = PatientLabRadiology::getOrderIds();
        HisPatientLabRadiology::savePatientLabRadiology($orderIds);
    }
}