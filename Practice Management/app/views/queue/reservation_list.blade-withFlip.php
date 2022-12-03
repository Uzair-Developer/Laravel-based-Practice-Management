<div id="cart">
    <div class="col-md-12" id="front">
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
                    $paginate = 25;
                    $clinicKey = 0;
                    $physicianKey = 0;
                    $patientKey = 0;
                    $physician = array();
                    $patients = array();
                    $countResOfPhy = array();
                    ?>
                    @if($clinics)
                        @foreach ($clinics as $key => $val)
                            <?php
                            $activePhyIds = UsersLocalizationClinics::getActivePhysiciansByClinicId($val, false, false, false);
                            ?>
                            @foreach ($activePhyIds as $key2 => $val2)
                                <?php

                                $reservations = Reservation::getAttendByClinic(null, array(
                                    'limit' => 5,
                                    'details' => true,
                                    'getYesterdayAfter24Hour' => true,
                                    'reception_call_flag' => 2, // get call done flag
                                ), $val2);
                                if (empty($reservations->toArray())) {
                                    continue;
                                }
                                if ($key % 2 == 0) {
                                    $background = '#E3DDDD';
                                } else {
                                    $background = '#FFF9F9';
                                }
                                ?>
                                @foreach($reservations as $key3 => $val3)
                                    <?php
                                    if ($paginate <= $key3) {
                                        $clinicKey = $key;
                                        $physicianKey = $key2;
                                        $patientKey = $key3;
                                        break 3;
                                    }
                                    if (!in_array($val3['id'], $patients)) {
                                        $patients[] = $val3['id'];
                                        $countResOfPhy[$val2][] = $val3['id'];
                                    }
                                    ?>
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
                                                         src="{{asset('dist/img/avatar5.png')}}"
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
                                        $code = explode('-', $val3['queue_code']);
                                        ?>
                                        <td class="en_td_font_size"
                                            style="vertical-align: middle; text-align: center;padding: 0;margin: 0;">
                                            <span style="font-size: 30px;font-weight: bold">{{$code[0] . $code[1]. $code[2]. '-' . $code[3]}}</span>
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

    @if(isset($clinics[$clinicKey]))
        <div class="col-md-12" id="back">
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
                        @if($clinics[$clinicKey])
                            @for($i = $clinicKey; $i < count($clinics); $i++)
                                <?php
                                $activePhyIds = UsersLocalizationClinics::getActivePhysiciansByClinicId($clinics[$i], false, false, false);
                                ?>
                                @for ($i2 = $physicianKey; $i2 < count($activePhyIds); $i2++)
                                    <?php
                                    $reservations = Reservation::getAttendByClinic(null, array(
                                        'limit' => 5,
                                        'details' => true,
                                        'getYesterdayAfter24Hour' => true,
                                        'reception_call_flag' => 2, // get call done flag
                                    ), $activePhyIds[$i2]);
                                    if (empty($reservations->toArray())) {
                                        continue;
                                    }
                                    if ($key % 2 == 0) {
                                        $background = '#E3DDDD';
                                    } else {
                                        $background = '#FFF9F9';
                                    }
                                    ?>
                                    @for ($i3 = $patientKey; $i3 < count($reservations); $i3++)
                                        <?php
                                        if (in_array($reservations[$i3]['id'], $patients)) {
                                            continue;
                                        } else {
                                            $patients[] = $reservations[$i3]['id'];
                                        }
                                        ?>
                                        <tr @if($reservations[$i3]['patient_status'] == \core\enums\PatientStatus::patient_in)
                                            style="background: {{$background}};background:#32cd32;"
                                            @elseif($reservations[$i3]['next_patient_flag'] == 1) class="highlight"
                                            @else style="background: {{$background}}" @endif>
                                            @if($i3 == $patientKey)
                                                <?php
                                                    if(isset($countResOfPhy[$activePhyIds[$i2]])){
                                                        $rowspan = abs(count($reservations->toArray()) - count($countResOfPhy[$activePhyIds[$i2]]));
                                                    } else {
                                                        $rowspan = count($reservations->toArray());
                                                    }
                                                ?>
                                                <td style="vertical-align: middle; text-align: center;background: {{$background}}"
                                                    rowspan="{{$rowspan}}"
                                                    class="en_td_font_size">
                                                    @if($reservations[$i3]['physician']['image_url'])
                                                        <img width="100" height="100"
                                                             src="{{asset($reservations[$i3]['physician']['image_url'])}}"
                                                             class="user-image"
                                                             title="{{$reservations[$i3]['physician']['first_name']}}"
                                                             alt="{{$reservations[$i3]['physician']['first_name']}}">
                                                    @else
                                                        <img width="100" height="100"
                                                             src="{{asset('dist/img/avatar5.png')}}"
                                                             class="user-image"
                                                             title="{{$reservations[$i3]['physician']['first_name']}}"
                                                             alt="{{$reservations[$i3]['physician']['first_name']}}">
                                                    @endif
                                                    <br>
                                                    {{$reservations[$i3]['physician']['first_name'] . ' ' . $reservations[$i3]['physician']['last_name']}}
                                                    <br>
                                                    <span class="arabic-section">{{$reservations[$i3]['physician']['first_name_ar'] . ' ' . $reservations[$i3]['physician']['last_name_ar']}}</span>
                                                </td>
                                                <td style="vertical-align: middle; text-align: center;background: {{$background}}"
                                                    rowspan="{{$rowspan}}"
                                                    class="en_td_font_size">
                                                    {{$reservations[$i3]['clinic']['name']}}
                                                    <br>
                                                    <span class="arabic-section">{{$reservations[$i3]['clinic']['name_ar']}}</span>
                                                </td>
                                            @endif
                                            <td class="en_td_font_size"
                                                style="vertical-align: middle; text-align: center;padding: 0;margin: 0;">
                                                @if($reservations[$i3]['type'] == 3)
                                                    <span style="font-size: 30px;font-weight: bold">{{date('h:i A', strtotime($reservations[$i3]['revisit_time_from']))}}</span>
                                                @elseif($reservations[$i3]['type'] == 1)
                                                    <span style="font-size: 30px;font-weight: bold">{{date('h:i A', strtotime($reservations[$i3]['time_from']))}}</span>
                                                @endif
                                            </td>
                                            <?php
                                            $code = explode('-', $reservations[$i3]['queue_code']);
                                            ?>
                                            <td class="en_td_font_size"
                                                style="vertical-align: middle; text-align: center;padding: 0;margin: 0;">
                                                <span style="font-size: 30px;font-weight: bold">{{$code[0] . $code[1]. $code[2]. '-' . $code[3]}}</span>
                                            </td>
                                            <td class="en_td_font_size"
                                                style="vertical-align: middle; text-align: center;">
                                                @if($reservations[$i3]['patient_status'] == \core\enums\PatientStatus::waiting)
                                                    <span>Waiting</span>
                                                @elseif($reservations[$i3]['patient_status'] == \core\enums\PatientStatus::patient_in)
                                                    @if($reservations[$i3]['patient_in_service'] != 2)
                                                        <span>Results</span>
                                                    @else
                                                        <span>In Clinic</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="ar_td_font_size arabic-section"
                                                style="vertical-align: middle; text-align: center;">
                                                @if($reservations[$i3]['patient_status'] == \core\enums\PatientStatus::waiting)
                                                    <span>منتظر</span>
                                                @elseif($reservations[$i3]['patient_status'] == \core\enums\PatientStatus::patient_in)
                                                    @if($reservations[$i3]['patient_in_service'] != 2)
                                                        <span>نتائج</span>
                                                    @else
                                                        <span>فى العياده</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endfor
                                    <?php
                                    $patientKey = 0;
                                    ?>
                                @endfor
                                <?php
                                $physicianKey = 0;
                                ?>
                            @endfor
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>


<style>
    .ar_td_font_size {
        font-size: 30px;
    }

    .en_td_font_size {
        font-size: 30px;
    }

</style>

<script>
    @if(count($patients) > $paginate)
    $("#card").off(".flip");
    $("#cart").flip({
        axis: "y", // y or x
        reverse: false, // true and false
        trigger: "click", // click or hover
        speed: '250',
        front: $('#front'),
        back: $('#back'),
        autoSize: false
    });
    $("#back").show();
    @else
    $("#card").off(".flip");
    $("#back").hide();
    @endif
</script>
<style>
    #cart {
        margin-top: 11% !important;
    }
</style>