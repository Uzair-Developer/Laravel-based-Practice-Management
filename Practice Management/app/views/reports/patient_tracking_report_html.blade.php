<div class="box">
    <!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-bordered" id="example1" cellspacing="0">
            <thead>
            <tr style="background: #4c9bff;text-align: center;">
                <th>P Name</th>
                <th>P ID</th>
                <th>Clinic</th>
                <th>Physician</th>
                <th>Visit Date/Time</th>
                <th>Attended Time</th>
                <th>Reception Call</th>
                <th>Ready On Queue</th>
                <th>Check IN</th>
                <th>In Service?</th>
                <th>Start Service</th>
                <th>Service Done</th>
                <th>Check Out</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Total Service Duration</th>
            </tr>
            </thead>
            <tbody>
            @foreach($report as $key => $val)
                <?php
                $physician = User::getById($val['physician_id']);
                $patient = Patient::getById($val['patient_id']);
                ?>
                <tr>
                    <td>
                        <div style="width: 150px;">
                            {{ucwords(strtolower($patient['name']))}}
                        </div>
                    </td>
                    <td>
                        <div style="width: 50px;">
                            {{$patient['registration_no']}}
                        </div>
                    </td>
                    <td>{{Clinic::getNameById($val['clinic_id'])}}</td>
                    <td>
                        <div style="width: 150px;">
                            {{ucwords(strtolower($physician['full_name']))}}
                        </div>
                    </td>
                    <td>{{$val['date']}}
                        @if($val['type'] == 1)
                            {{$val['time_from']}}
                        @elseif($val['type'] == 3)
                            {{$val['revisit_time_from']}}
                        @endif
                    </td>
                    <td>
                        @if($val['patient_attend_datetime'])
                            <div style="width: 150px;">
                                {{date('H:i:s', strtotime($val['patient_attend_datetime']))}}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($val['reception_call_datetime'])
                            <div style="width: 150px;">
                                {{date('H:i:s', strtotime($val['reception_call_datetime']))}}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($val['reception_call_done_datetime'])
                            <div style="width: 150px;">
                                {{date('H:i:s', strtotime($val['reception_call_done_datetime']))}}</div>
                        @endif
                    </td>
                    <td>{{$val['actual_time_from']}}</td>
                    <td>
                        @if($val['patient_in_service'] == 2)
                            <span style="color: orange">No</span>
                        @else
                            <span style="color: green">Yes</span>

                        @endif
                    </td>
                    <td>{{$val['in_service_time']}}</td>
                    <td>{{$val['service_done_time']}}</td>
                    <td>{{$val['actual_time_to']}}</td>
                    <td>
                        <div style="width: 200px;">
                            <?php
                            if ($val['create_by']) {
                                $oneUser = Sentry::findUserById($val['create_by']);
                                $groups = $oneUser->getGroups();
                                $count = count($groups);
                                $create_by = User::getName($val['create_by']) . ' (';
                                foreach ($groups as $index2 => $val2) {
                                    if ($count == $index2 + 1) {
                                        $create_by .= $val2['name'] . ')';
                                    } else {
                                        $create_by .= $val2['name'] . ', ';
                                    }
                                }
                            } else {
                                $create_by = User::getName($val['create_by']);
                            }
                            ?>
                            {{$create_by}}
                        </div>
                    </td>
                    <td>{{$val['created_at']}}</td>
                    <td>
                        @if($val['patient_attend_datetime'] && $val['actual_time_to'])
                            {{Functions::makeTime2D(Functions::dateDifference(date('H:i:s', strtotime($val['patient_attend_datetime'])),$val['actual_time_to'], '%h:%i:%s'))}}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $('#example1').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "pageLength": 100,
        "order": [[4, "asc"]],
        "sScrollY": "400",
        "sScrollX": "100%",
        "sScrollXInner": "150%",
        "bScrollCollapse": true
    });
</script>