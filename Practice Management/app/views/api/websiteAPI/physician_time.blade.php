<style>
    .shift_1 {
        border-left: 5px solid #00a157;
    }

    .shift_2 {
        border-left: 5px solid blue;
        background: lightgrey;
    }

    .shift_3 {
        border-left: 5px solid #db8b0b;
    }
</style>
<div class="col-md-12">
    <div class="box box-primary">
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="text-align: center" width="15px">
                        @if(isset($lang) && $lang == 'ar')
                            من
                        @else
                            From
                        @endif
                    </th>
                    <th style="text-align: center" width="15px">
                        @if(isset($lang) && $lang == 'ar')
                            الى
                        @else
                            To
                        @endif
                    </th>
                    <th style="text-align: center">
                        @if(isset($lang) && $lang == 'ar')
                            العياده
                        @else
                            Clinic
                        @endif
                    </th>
                    <th style="text-align: center">
                        @if(isset($lang) && $lang == 'ar')
                            الدكتور
                        @else
                            Physician
                        @endif
                    </th>
                    <th style="text-align: center">
                        @if(isset($lang) && $lang == 'ar')
                            التاريخ
                        @else
                            Date
                        @endif
                    </th>
                    <th style="text-align: center">
                        @if(isset($lang) && $lang == 'ar')
                            إختيارات
                        @else
                            Options
                        @endif
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($availableTimes as $key => $val)
                    @if(isset($val['reserved']))
                        <?php continue; ?>
                    @endif
                    <tr class="@if(isset($val['shift'])) shift_{{$val['shift']}} @endif" style="
                    @if(isset($val['reserved']))
                    @if($val['patient_attend'] == 1)
                            background:#84e184;
                    @endif
                    @if($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                            background:#32cd32;
                    @endif
                    @if($val['patient_status'] == \core\enums\PatientStatus::patient_out)
                            background:deepskyblue;
                    @endif
                    @if($val['patient_status'] == \core\enums\PatientStatus::cancel)
                            background:#ff8566;
                    @endif
                    @if($val['patient_status'] == \core\enums\PatientStatus::no_show)
                            background:#ff8566;
                    @endif
                    @if($val['patient_status'] == \core\enums\PatientStatus::pending)
                            background:#ffb84d;
                    @endif
                    @if($val['patient_status'] == \core\enums\PatientStatus::archive)
                            background:#ff8566;
                    @endif
                    @endif ">
                        @if($val['time'] >= date('H:i:s'))
                            <td style="text-align: center">
                                @if($val['time'] > '23:59:00')
                                    <?php
                                    $seconds = Functions::hoursToSeconds($val['time']);
                                    $newSeconds = $seconds - (24 * 60 * 60);
                                    $time = Functions::timeFromSeconds($newSeconds);
                                    ?>
                                    {{$time}}
                                @else
                                    {{$val['time']}}
                                @endif
                            </td>
                            <td style="text-align: center">
                                @if(isset($val['time_to']))
                                    @if($val['time_to'] > '23:59:00')
                                        <?php
                                        $seconds = Functions::hoursToSeconds($val['time_to']);
                                        $newSeconds = $seconds - (24 * 60 * 60);
                                        $time_to = Functions::timeFromSeconds($newSeconds);
                                        ?>
                                        {{$time_to}}
                                    @else
                                        {{$val['time_to']}}
                                    @endif
                                @else
                                    <?php
                                    $seconds = Functions::hoursToSeconds($val['time']);
                                    $newSeconds = $seconds + ($slots * 60);
                                    $time_to = Functions::timeFromSeconds($newSeconds);
                                    if ($time_to > '23:59:00') {
                                        $seconds = Functions::hoursToSeconds($time_to);
                                        $newSeconds = $seconds - (24 * 60 * 60);
                                        $time_to = Functions::timeFromSeconds($newSeconds);
                                    }
                                    ?>
                                    {{$time_to}}
                                @endif
                            </td>
                            <td style="text-align: center">
                                @if(isset($lang) && $lang == 'ar')
                                    {{$clinic['name_ar']}}
                                @else
                                    {{$clinic['name']}}
                                @endif
                            </td>
                            <td style="text-align: center">
                                @if(isset($lang) && $lang == 'ar')
                                    {{$physician['first_name_ar'] . ' ' . $physician['last_name_ar']}}
                                @else
                                    {{$physician['full_name']}}
                                @endif
                            </td>
                            <td style="text-align: center">
                                {{$selectDate}}
                            </td>
                            <td style="text-align: center">
                                @if(isset($val['reserved']))
                                @else
                                    <button type="button" time="{{$val['time']}}" to_time="@if(isset($val['time_to']))
                                    {{$val['time_to']}}
                                    @else
                                    <?php
                                    $seconds = Functions::hoursToSeconds($val['time']);
                                    $newSeconds = $seconds + ($slots * 60);
                                    $time = Functions::timeFromSeconds($newSeconds);
                                    ?>
                                    {{$time}}
                                    @endif" title="Reserve"
                                            class="button subbutton btn btn-primary reserveBtn">
                                        @if(isset($lang) && $lang == 'ar')
                                            إحجز الأن
                                        @else
                                            Reserve
                                        @endif
                                    </button>
                                @endif

                            </td>
                    </tr>
                    @endif
                @endforeach
                @if(empty($availableTimes))
                    <tr>
                        <td colspan="10">
                            <center style="color: red">
                                @if(isset($lang) && $lang == 'ar')
                                    غير متاح!
                                @else
                                    No Available!
                                @endif
                            </center>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>