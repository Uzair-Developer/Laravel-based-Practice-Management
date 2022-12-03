<?php

class RiyadhPatient extends Eloquent
{
    protected $table = 'dbo.PATIENT';
    protected $guarded = array('');
    protected $connection = 'sqlsrv3';
    public $timestamps = false;

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll()
    {
        self::whereIn('INTEGRATIONSTATUS', array('HIS_NEW', 'HIS_UPDATE'))->chunk(100, function ($patients) {
            foreach ($patients as $key => $val) {
                $fName = $val['FIRSTNAME'] ? $val['FIRSTNAME'] : '';
                $mName = $val['MIDDLENAME'] ? ' ' . $val['MIDDLENAME'] : '';
                $lName = $val['LASTNAME'] ? ' ' . $val['LASTNAME'] : '';
                $fmName = $val['FAMILYNAME'] ? ' ' . $val['FAMILYNAME'] : '';
                $name = $fName . $mName . $lName . $fmName;
                if ($val['PATIENTID']) {
                    if ($val['REGISTRATIONNO']) {
                        $patientArray = array(
                            'issue_authority_code' => $val['ISSUEAUTHORITYCODE'],
                            'registration_no' => $val['REGISTRATIONNO'],
                            'title_id' => $val['TITLE'],
                            'name' => $name,
                            'first_name' => $val['FIRSTNAME'] ? $val['FIRSTNAME'] : '',
                            'middle_name' => $val['MIDDLENAME'] ? $val['MIDDLENAME'] : '',
                            'last_name' => $val['LASTNAME'] ? $val['LASTNAME'] : '',
                            'family_name' => $val['FAMILYNAME'] ? $val['FAMILYNAME'] : '',
                            'email' => $val['PEMAIL'],
                            'birthday' => explode(' ', $val['DATEOFBIRTH'])[0],
                            'age' => $val['AGE'],
                            'age_type_id' => $val['AGETYPE'],
                            'gender' => $val['SEX'],
                            'marital_status_id' => $val['MARITALSTATUS'],
                            'country_id' => $val['COUNTRY'],
                            'city_id' => $val['PCITY'],
                            'nationality_id' => $val['NATIONALITY'],
                            'address' => $val['ADDRESS1'],
                        );
                        if($val['IDENTIFICATIONNO']) {
                            $patientArray['national_id'] = $val['IDENTIFICATIONNO'];
                        } else {
                            $patientArray['national_id'] = '00000000000';
                        }
                        if($val['PCELLNO']) {
                            $patientArray['phone'] = $val['PCELLNO'];
                        } else {
                            if($val['PPHONE']) {
                                $patientArray['phone'] = $val['PPHONE'];
                            } else {
                                $patientArray['phone'] = '0000000000';
                            }
                        }
                        Patient::edit($patientArray, $val['PATIENTID']);
                        self::edit(array(
                            'MERGE_FLAG' => 'Y',
                            'INTEGRATIONSTATUS' => 'PROCEED',
                        ), $val['ID']);
                    }
                } else {
                    $patientArray = array(
                        'hospital_id' => 2,
                        'issue_authority_code' => $val['ISSUEAUTHORITYCODE'],
                        'registration_no' => $val['REGISTRATIONNO'],
                        'title_id' => $val['TITLE'],
                        'name' => $name,
                        'first_name' => $val['FIRSTNAME'] ? $val['FIRSTNAME'] : '',
                        'middle_name' => $val['MIDDLENAME'] ? ' ' . $val['MIDDLENAME'] : '',
                        'last_name' => $val['LASTNAME'] ? ' ' . $val['LASTNAME'] : '',
                        'family_name' => $val['FAMILYNAME'] ? ' ' . $val['FAMILYNAME'] : '',
                        'email' => $val['PEMAIL'],
                        'birthday' => explode(' ', $val['DATEOFBIRTH'])[0],
                        'age' => $val['AGE'],
                        'age_type_id' => $val['AGETYPE'],
                        'gender' => $val['SEX'],
                        'marital_status_id' => $val['MARITALSTATUS'],
                        'country_id' => $val['COUNTRY'],
                        'city_id' => $val['PCITY'],
                        'nationality_id' => $val['NATIONALITY'],
                        'address' => $val['ADDRESS1'],
                        'sync_flag' => 0,
                    );
                    if($val['IDENTIFICATIONNO']) {
                        $patientArray['national_id'] = $val['IDENTIFICATIONNO'];
                    } else {
                        $patientArray['national_id'] = '00000000000';
                    }
                    if($val['PCELLNO']) {
                        $patientArray['phone'] = $val['PCELLNO'];
                    } else {
                        if($val['PPHONE']) {
                            $patientArray['phone'] = $val['PPHONE'];
                        } else {
                            $patientArray['phone'] = '0000000000';
                        }
                    }
                    $checkPatient = Patient::getByRegistrationNo($val['REGISTRATIONNO'], 2);
                    if ($checkPatient) {
                        Patient::edit($patientArray, $checkPatient['id']);
                        $patientId = $checkPatient['id'];
                    } else {
                        $patient = Patient::add($patientArray);
                        $patientId = $patient->id;
                    }
                    self::edit(array(
                        'PATIENTID' => $patientId,
                        'MERGE_FLAG' => 'Y',
                        'INTEGRATIONSTATUS' => 'PROCEED',
                    ), $val['ID']);
                }
            }
        });
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getByRegNo($regNo)
    {
        return self::where('REGISTRATIONNO', $regNo)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function sendNewPatientsToHIS()
    {
        Patient::where('sync_flag', 0)->chunk(50, function ($patients) {
            foreach ($patients as $key => $val) {
                $hospitalData = Hospital::getById($val['hospital_id']);
                $addPatient = array(
                    'PATIENTID' => $val['id'],
                    'ISSUEAUTHORITYCODE' => $hospitalData['issue_authority_code'],
                    'PPHONE' => $val['phone'],
                    'PCELLNO' => $val['phone'],
                    'FIRSTNAME' => $val['first_name'] ? $val['first_name'] : '.',
                    'MIDDLENAME' => $val['middle_name'] ? $val['middle_name'] : '.',
                    'LASTNAME' => $val['last_name'] ? $val['last_name'] : '.',
                    'FAMILYNAME' => $val['family_name'] ? $val['family_name'] : '.',
                    'IDENTIFICATIONNO' => $val['national_id'],
                    'TITLE' => 1,
                    'PEMAIL' => $val['email'],
                    'DATEOFBIRTH' => Functions::fixDate($val['birthday']),
                    'AGE' => $val['age'],
                    'AGETYPE' => $val['age_type_id'],
                    'SEX' => $val['gender'],
                    'ADDRESS1' => $val['address'],
                    'REGDATETIME' => date('Y-m-d H:i:s'),
                    'MERGE_FLAG' => 1,
                    'INTEGRATIONSTATUS' => 'PMS_NEW',
                );
                $his_patient = self::add($addPatient);
                if ($his_patient) {
                    Patient::edit(array(
                        'sync_flag' => 1
                    ), $val['id']);
                }
            }
        });
    }
}
