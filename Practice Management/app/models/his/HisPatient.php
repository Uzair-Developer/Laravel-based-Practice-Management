<?php

class HISPatient extends Eloquent
{
    protected $table = 'dbo.PATIENT';
    protected $guarded = array('');
    protected $connection = 'sqlsrv';
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
                            'middle_name' => $val['MIDDLENAME'] ? ' ' . $val['MIDDLENAME'] : '',
                            'last_name' => $val['LASTNAME'] ? ' ' . $val['LASTNAME'] : '',
                            'family_name' => $val['FAMILYNAME'] ? ' ' . $val['FAMILYNAME'] : '',
                            'phone' => $val['PPHONE'],
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
                        $callerInfo = CallerInfo::getByPhone($val['PPHONE'], 1);
                        if($callerInfo){
                            $patientArray['caller_id'] = $callerInfo['id'];
                        } else {
                            $newCallerInfo = CallerInfo::add(array(
                                'phone' => $val['PPHONE'],
                                'name' => $name,
                                'hospital_id' => 1,
                            ));
                            $patientArray['caller_id'] = $newCallerInfo->id;
                        }
                        Patient::edit($patientArray, $val['PATIENTID']);
                        HISPatient::edit(array(
                            'MERGE_FLAG' => 'Y',
                            'INTEGRATIONSTATUS' => 'PROCEED',
                        ), $val['ID']);
                    }
                } else {
                    $patientArray = array(
                        'hospital_id' => 1,
                        'issue_authority_code' => $val['ISSUEAUTHORITYCODE'],
                        'registration_no' => $val['REGISTRATIONNO'],
                        'title_id' => $val['TITLE'],
                        'name' => $name,
                        'first_name' => $val['FIRSTNAME'] ? $val['FIRSTNAME'] : '',
                        'middle_name' => $val['MIDDLENAME'] ? ' ' . $val['MIDDLENAME'] : '',
                        'last_name' => $val['LASTNAME'] ? ' ' . $val['LASTNAME'] : '',
                        'family_name' => $val['FAMILYNAME'] ? ' ' . $val['FAMILYNAME'] : '',
                        'phone' => $val['PPHONE'],
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
                    $callerInfo = CallerInfo::getByPhone($val['PPHONE'], 1);
                    if($callerInfo){
                        $patientArray['caller_id'] = $callerInfo['id'];
                    } else {
                        $newCallerInfo = CallerInfo::add(array(
                            'phone' => $val['PPHONE'],
                            'name' => $name,
                            'hospital_id' => 1,
                        ));
                        $patientArray['caller_id'] = $newCallerInfo->id;
                    }

                    $patient = Patient::add($patientArray);
                    HISPatient::edit(array(
                        'PATIENTID' => $patient->id,
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

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }
}
