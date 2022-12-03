<?php

class HisPatientLabRadiology extends Eloquent
{
    protected $table = 'dbo.PatientLabRadiologyInvestigation';
    protected $guarded = array('');
    protected $connection = 'sqlsrv2';
    public $timestamps = false;


    public static function savePatientLabRadiology($orderIds)
    {
        $date = date('Y-m-d', strtotime("-2 days"));
        self::whereNotIn('OrderID', $orderIds)
            ->where('datetime', '>=', $date . ' 00:00:00.000')
            ->chunk(100, function ($orders) {
                foreach ($orders as $key => $val) {
                    $patient = Patient::getByRegistrationNo($val['Registrationno'], 1);
                    $physician = User::checkHisExist($val['Doctor_Id']);
                    if ($patient) {
                        $addedArray = array(
                            'patient_id' => $patient['id'],
                            'patient_reg_no' => $val['Registrationno'],
                            'order_id' => $val['OrderID'],
                            'test_id' => $val['TestId'],
                            'test_name' => $val['TestName'],
                            'station' => $val['Station'],
                            'physician_his_id' => $val['Doctor_Id'],
                            'physician_name' => $val['DoctorName'],
                            'datetime' => $val['datetime'],
                            'verifieddatetime' => $val['verifieddatetime'],
                        );
                        if ($physician) {
                            $addedArray['physician_id'] = $physician['id'];
                        }
                        PatientLabRadiology::add($addedArray);
                    }
                }
            });
    }

    public static function getByOrderId($order_id)
    {
        return self::where('OrderID', $order_id)->get()->toArray();
    }

    public static function getByOrder_PatientReg($order_id, $patient_reg_no)
    {
        return self::where('OrderID', $order_id)
            ->where('Registrationno', $patient_reg_no)
            ->get()->toArray();
    }
}
