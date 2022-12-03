<?php

class ReservationTest extends Eloquent
{
    protected $table = 'reservation_test';
    protected $guarded = array('');

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs = '')
    {
        $data = self::where(function ($q) use ($inputs) {
            if (isset($inputs['type']) && $inputs['type']) {
                $q->where('type', $inputs['type']);
            }
        });
        if (isset($inputs['paginate'])) {
            $data = $data->paginate($inputs['paginate']);
        } else {
            $data = $data->get();
        }

        return $data;
    }

    public static function getChunk()
    {
        self::where('send', 2)->chunk(25, function ($res) {
            foreach ($res as $key => $val) {
                $reservation = Reservation::getById($val['reservation_id']);
                if ($reservation) {
                    $reservation_id = $reservation['id'];
                    if ($reservation['sms_lang'] == 1) { // arabic
                        $body = 'مستشفى السعودي الألماني تتشرف بدعوتكم لإبداء الرأي عن الخدمة المقدمة لكم أثناء زيارتكم لها عبر هذا الرابط\nsghcairo.com/index.php/ar/survey?c=' . $reservation_id . '\nونعلمكم انه ابتداءا من اليوم يمكنكم عمل الحجز للعيادات الخارجية عن طريق موقعنا لمن سبق لهم إصدار ملف طب';
                    } else { // english
                        $body = 'Dear valued guest.\nAt SGH, we are constantly trying to improve our service and would like to hear your feedback on how we performed - please spend few minutes to let us know how to serve you better through this link\nsghcairo.com/index.php/ar/survey?c=' . $reservation_id . '\nAlso thought our website you can make your next reservation  (only for registered patients who own a pin no. of the hospital)';
                    }
                    $patient = Patient::getById($reservation['patient_id']);
                    if ($patient) {
                        Functions::sendSMS($patient['phone'], $body);
                        ReservationTest::edit(array('send' => 1), $val['id']);
                    }
                }
            }
        });
    }

    public static function getById($id)
    {
        return self::where('reservation_id', $id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }
}
