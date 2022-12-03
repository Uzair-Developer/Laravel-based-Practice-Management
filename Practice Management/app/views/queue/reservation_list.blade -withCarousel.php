<div class="col-md-12" id="cart">
    <div class="box box-primary">
        <div class="box-header">
        </div>
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                <tr style="font-size: 22px;">
                    <th style="text-align: center">
                        <span class="arabic-section">الطبيب</span>
                        <br>
                        <span>Doctor</span>
                    </th>
                    <th style="text-align: center">
                        <span class="arabic-section">العياده</span>
                        <br>
                        <span>Clinic </span>
                    </th>
                    <th style="text-align: center">
                        <span class="arabic-section">الوقت</span>
                        <br>
                        <span>Time</span>
                    </th>
                    <th style="text-align: center">
                        <span class="arabic-section">رقم الحجز</span>
                        <br>
                        <span>Res Num</span>
                    </th>
                    <th style="text-align: center">
                        <span>Status</span>
                    </th>
                    <th style="text-align: center">
                        <span class="arabic-section">الحاله</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                $physician = array();
                $countPhysician = 0;
                ?>
                @if($clinics)
                    @foreach ($clinics as $key => $val)
                        <?php
                        $activePhyIds = UsersLocalizationClinics::getActivePhysiciansByClinicId($val, false, false, false);
                        ?>
                        @foreach ($activePhyIds as $key2 => $val2)
                            <?php
                            if (in_array($val2, $physician)) {
                                continue;
                            } else {
                                $physician[] = $val2;
                            }
                            $reservations = Reservation::getAttendByClinic(null, array(
                                    'limit' => 4,
                                    'details' => true,
                                    'getYesterdayAfter24Hour' => true,
                                    'reception_call_flag' => 2, // get call done flag
                            ), $val2);
                            if (empty($reservations->toArray())) {
                                continue;
                            }
                            if ($countPhysician % 2 == 0) {
                                $background = '#E3DDDD';
                            } else {
                                $background = '#ffffff';
                            }
                            $countPhysician++;
                            ?>
                            @foreach($reservations as $key3 => $val3)
                                <tr @if($val3['patient_status'] == \core\enums\PatientStatus::patient_in)
                                    style="background: {{$background}};background:#32cd32;"
                                    @elseif($val3['next_patient_flag'] == 1) class="highlight"
                                    @else style="background: {{$background}}" @endif>
                                    @if($key3 == 0)
                                        <td style="vertical-align: middle; text-align: center;background: {{$background}}"
                                            rowspan="{{count($reservations->toArray())}}" class="en_td_font_size">
                                            @if($val3['physician']['image_url'])
                                                <img width="100" height="100"
                                                     src="{{asset($val3['physician']['image_url'])}}"
                                                     class="user-image"
                                                     title="{{$val3['physician']['first_name']}}"
                                                     alt="{{$val3['physician']['first_name']}}">
                                            @else
                                                <img width="100" height="100"
                                                     src="{{asset('images/anonymous.gif')}}"
                                                     class="user-image"
                                                     title="{{$val3['physician']['first_name']}}"
                                                     alt="{{$val3['physician']['first_name']}}">
                                            @endif
                                            <br>
                                            {{$val3['physician']['first_name'] . ' ' . $val3['physician']['last_name']}}
                                            <br>
                                            <span class="arabic-section">{{$val3['physician']['first_name_ar'] . ' ' . $val3['physician']['last_name_ar']}}</span>
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;background: {{$background}}"
                                            rowspan="{{count($reservations->toArray())}}" class="en_td_font_size">
                                            {{$val3['clinic']['name']}}
                                            <br>
                                            <span class="arabic-section">{{$val3['clinic']['name_ar']}}</span>
                                        </td>
                                    @endif
                                    <td class="en_td_font_size"
                                        style="vertical-align: middle; text-align: center;padding: 0;margin: 0;">
                                        @if($val3['type'] == 3)
                                            <span style="font-size: 30px;font-weight: bold">{{date('h:i A', strtotime($val3['revisit_time_from']))}}</span>
                                        @elseif($val3['type'] == 1)
                                            <span style="font-size: 30px;font-weight: bold">{{date('h:i A', strtotime($val3['time_from']))}}</span>
                                        @endif
                                    </td>
                                    <?php
                                    if (empty($val3['queue_code'])) {
                                        $queueCode = '';
                                        if ($val3['type'] == 1) {
                                            $queueCode = Reservation::buildQueueCode(1, $val3['clinic_id'], $val3['physician_id'],
                                                    $val3['date'], $val3['time_from']);
                                        } else if ($val3['type'] == 2) {
                                            $queueCode = Reservation::buildQueueCode(2, $val3['clinic_id'], $val3['physician_id'],
                                                    $val3['date'], null);
                                        } else if ($val3['type'] == 3) {
                                            $queueCode = Reservation::buildQueueCode(3, $val3['clinic_id'], $val3['physician_id'],
                                                    $val3['date'], $val3['revisit_time_from']);
                                        }
                                        Reservation::edit([
                                                'queue_code' => $queueCode
                                        ], $val3['id']);
                                        $val3['queue_code'] = $queueCode;
                                    }
                                    $code = explode('-', $val3['queue_code']);
                                    ?>
                                    <td class="en_td_font_size"
                                        style="vertical-align: middle; text-align: center;padding: 0;margin: 0;">
                                        <span style="font-size: 30px;font-weight: bold">
                                            @if(isset($code[0]) && isset($code[1]) && isset($code[2]) && isset($code[3]))
                                                {{$code[0] . $code[1]. $code[2]. '-' . $code[3]}}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="en_td_font_size" style="vertical-align: middle; text-align: center;">
                                        @if($val3['patient_status'] == \core\enums\PatientStatus::waiting)
                                            <span>Waiting</span>
                                        @elseif($val3['patient_status'] == \core\enums\PatientStatus::patient_in)
                                            @if($val3['patient_in_service'] != 2)
                                                <span>Results</span>
                                            @else
                                                <span>In Clinic</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="ar_td_font_size arabic-section"
                                        style="vertical-align: middle; text-align: center;">
                                        @if($val3['patient_status'] == \core\enums\PatientStatus::waiting)
                                            <span>منتظر</span>
                                        @elseif($val3['patient_status'] == \core\enums\PatientStatus::patient_in)
                                            @if($val3['patient_in_service'] != 2)
                                                <span>نتائج</span>
                                            @else
                                                <span>فى العياده</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


<style>
    .ar_td_font_size {
        font-size: 30px;
    }

    .en_td_font_size {
        font-size: 30px;
    }

    #cart {
        margin-top: 11% !important;
    }
</style>