<div class="col-md-6">
    <div class="box box-primary">
        <div class="box-header">
            <img style="float: right" src="{{asset('images/sgh-logo.jpg')}}" height="50">
            <h4 style="font-size: 18px;">Clinic Name: <span style="color: green"
                                                            id="clinic_name">{{$clinic['name']}}</span></h4>
        </div>
        <div class="box-body" id="reservationTable">
            <table class="table table-bordered" id="">
                <thead>
                <tr style="font-size: 18px;">
                    <th>Physician Name</th>
                    <th>Res. Code</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @if(!$reservations->isEmpty())
                    @foreach($reservations as $key => $val)
                        <tr
                                @if($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                                style="background:#32cd32;"
                                @elseif($val['next_patient_flag'] == 1) class="highlight"
                                @endif>
                            <td>{{$val['physician']['first_name']. ' ' . $val['physician']['last_name']}}</td>
                            <?php $code = explode('-', $val['code']); ?>
                            <td>{{$code[0] . '-' . $code[1]}}</td>
                            <td>
                                @if($val['type'] == 3)
                                    {{date('h:i A', strtotime($val['revisit_time_from']))}}
                                @elseif($val['type'] == 1)
                                    {{date('h:i A', strtotime($val['time_from']))}}
                                @endif
                            </td>
                            <td>
                                @if($val['patient_status'] == \core\enums\PatientStatus::waiting)
                                    <span>Waiting</span>
                                @elseif($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                                    <span>In Clinic</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="col-md-6 arabic-section">
    <div class="box box-primary">
        <div class="box-header">
            <img src="{{asset('images/sgh-logo.jpg')}}" height="50">
            <h4 class="arabic-section" style="float: right">إسم العياده: <span style="color: green"
                                                                               id="clinic_name">{{$clinic['name_ar']}}</span>
            </h4>
        </div>
        <div class="box-body" id="reservationTable">
            <table class="table table-bordered" id="" style="text-align: right;">
                <thead>
                <tr style="font-size: 18px;">
                    <th style="text-align: right;">الحاله</th>
                    <th style="text-align: right;">الوقت</th>
                    <th style="text-align: right;">رقم الحجز</th>
                    <th style="text-align: right;">إسم الدكتور</th>
                </tr>
                </thead>
                <tbody>
                @if(!$reservations->isEmpty())
                    @foreach($reservations as $key => $val)
                        <tr
                                @if($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                                style="background:#32cd32;"
                                @elseif($val['next_patient_flag'] == 1) class="highlight"
                                @endif>
                            <td>
                                @if($val['patient_status'] == \core\enums\PatientStatus::waiting)
                                    <span>منتظر</span>
                                @elseif($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                                    <span>فى العياده</span>
                                @endif
                            </td>
                            <td>
                                @if($val['type'] == 3)
                                    {{date('h:i A', strtotime($val['revisit_time_from']))}}
                                @elseif($val['type'] == 1)
                                    {{date('h:i A', strtotime($val['time_from']))}}
                                @endif
                            </td>
                            <?php $code = explode('-', $val['code']); ?>
                            <td>{{$code[0] . '-' . $code[1]}}</td>
                            <td>{{$val['physician']['first_name_ar']. ' ' . $val['physician']['last_name_ar']}}</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
