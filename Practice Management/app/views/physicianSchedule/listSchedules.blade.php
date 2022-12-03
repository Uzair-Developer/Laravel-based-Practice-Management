<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<table class="table table-bordered" id="example2">
    <thead>
    <tr>
        <th>Date</th>
        <th>From Time</th>
        <th>To Time</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $from_date = $physicianSchedule['start_date'];
    $to_date = $physicianSchedule['end_date'];
    $daysName = array(
            'saturday' => 'sat',
            'sunday' => 'sun',
            'monday' => 'mon',
            'tuesday' => 'tues',
            'wednesday' => 'wed',
            'thursday' => 'thurs',
            'friday' => 'fri',
    );
    ?>
    @while(true)
        <?php
        $checkException = PhysicianScheduleException::checkByClinic_Physician_Date($physicianSchedule['clinic_id'], $physicianSchedule['user_id'], $from_date);
        $inputDayName = lcfirst(date('l', strtotime($from_date)));
        ?>
        @if($checkException)
            @if($checkException['shift1_dayoff'] != 1 || $checkException['shift2_dayoff'] != 1
            || $checkException['shift3_dayoff'] != 1)
                <tr>
                    <td>{{$from_date . ' ' . date('l', strtotime($from_date)) }}</td>
                    <td>
                        @if($checkException['shift1_dayoff'] != 1)
                            {{$checkException['shift1_time_from']}}
                        @endif
                        @if($checkException['num_of_shifts'] == 2 || $checkException['num_of_shifts'] == 3)
                            @if($checkException['shift2_dayoff'] != 1)
                                <br>
                                {{$checkException['shift2_time_from']}}
                            @endif
                            @if($checkException['num_of_shifts'] == 3)
                                @if($checkException['shift3_dayoff'] != 1)
                                    <br>
                                    {{$checkException['shift3_time_from']}}
                                @endif
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($checkException['shift1_dayoff'] != 1)
                            {{$checkException['shift1_time_to']}}
                        @endif
                        @if($checkException['num_of_shifts'] == 2 || $checkException['num_of_shifts'] == 3)
                            @if($checkException['shift2_dayoff'] != 1)
                                <br>
                                {{$checkException['shift2_time_to']}}
                            @endif
                            @if($checkException['num_of_shifts'] == 3)
                                @if($checkException['shift3_dayoff'] != 1)
                                    <br>
                                    {{$checkException['shift3_time_to']}}
                                @endif
                            @endif
                        @endif
                    </td>
                </tr>
            @endif
        @else
            @if(strpos($physicianSchedule['dayoff_1'], $inputDayName) === false
        || strpos($physicianSchedule['dayoff_2'], $inputDayName) === false
        || strpos($physicianSchedule['dayoff_3'], $inputDayName) === false)
                <tr>
                    <td>{{$from_date . ' ' . date('l', strtotime($from_date))}}</td>
                    <td>
                        @if(strpos($physicianSchedule['dayoff_1'], $inputDayName) === false)
                            <?php $startTime = $physicianSchedule[$daysName[$inputDayName] . '_start_time_1']; ?>
                            {{$startTime}}
                        @endif
                        @if($physicianSchedule['num_of_shifts'] == 2 || $physicianSchedule['num_of_shifts'] == 3)
                            @if(strpos($physicianSchedule['dayoff_2'], $inputDayName) === false)
                                <?php $startTime = $physicianSchedule[$daysName[$inputDayName] . '_start_time_2']; ?>
                                <br>
                                {{$startTime}}
                            @endif
                            @if($physicianSchedule['num_of_shifts'] == 3)
                                @if(strpos($physicianSchedule['dayoff_3'], $inputDayName) === false)
                                    <?php $startTime = $physicianSchedule[$daysName[$inputDayName] . '_start_time_3']; ?>
                                    <br>
                                    {{$startTime}}
                                @endif
                            @endif
                        @endif
                    </td>
                    <td>
                        @if(strpos($physicianSchedule['dayoff_1'], $inputDayName) === false)
                            <?php $endTime = $physicianSchedule[$daysName[$inputDayName] . '_end_time_1']; ?>
                            {{$endTime}}
                        @endif
                        @if($physicianSchedule['num_of_shifts'] == 2 || $physicianSchedule['num_of_shifts'] == 3)
                            @if(strpos($physicianSchedule['dayoff_2'], $inputDayName) === false)
                                <?php $endTime = $physicianSchedule[$daysName[$inputDayName] . '_end_time_2']; ?>
                                <br>
                                {{$endTime}}
                            @endif
                            @if($physicianSchedule['num_of_shifts'] == 3)
                                @if(strpos($physicianSchedule['dayoff_3'], $inputDayName) === false)
                                    <?php $endTime = $physicianSchedule[$daysName[$inputDayName] . '_end_time_3']; ?>
                                    <br>
                                    {{$endTime}}
                                @endif
                            @endif
                        @endif
                    </td>
                </tr>
            @endif
        @endif

        @if($from_date == $to_date)
            <?php break; ?>
        @else
            <?php $from_date = date('Y-m-d', strtotime("+1 day", strtotime($from_date))); ?>
        @endif
    @endwhile

    </tbody>
</table>

<script>
    $('#example2').DataTable({
        "paging": false,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "sScrollY": "400px",
        "sScrollX": "100%",
        "sScrollXInner": "100%",
        "bScrollCollapse": true
    });
</script>