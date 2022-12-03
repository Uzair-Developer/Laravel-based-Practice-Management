<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Action</th>
            <th>Action By</th>
            <th>Created At</th>
            <th>Clinic Name</th>
            <th>Physician Name</th>
            <th>Patient Name</th>
            <th>Date</th>
            <th>Time From</th>
            <th>Time To</th>
            <th>Patient Phone</th>
            <th>Patient ID</th>
            <th>Reservation Code</th>
            <th>Reservation Status</th>
            <th>Patient Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($reservations as $key => $val)
            <?php
            $physician = User::getById($val['physician_id']);
            $patient = Patient::getById($val['patient_id']);
            ?>
            <tr>
                <td>{{$val['action']}}</td>
                <td>
                    <div style="width: 150px;">
                        <?php
                        if ($val['action_by']) {
                            $oneUser = Sentry::findUserById($val['action_by']);
                            $groups = $oneUser->getGroups();
                            $count = count($groups);
                            $create_by = User::getName($val['action_by']) . ' (';
                            foreach ($groups as $index2 => $val2) {
                                if ($count == $index2 + 1) {
                                    $create_by .= $val2['name'] . ')';
                                } else {
                                    $create_by .= $val2['name'] . ', ';
                                }
                            }
                        } else {
                            $create_by = 'By System';
                        }
                        ?>
                        {{$create_by}}
                    </div>
                </td>
                <td>{{$val['created_at']}}</td>
                <td>{{Clinic::getNameById($val['clinic_id'])}}</td>
                <td>
                    <div style="width: 150px;">
                        {{ucwords(strtolower($physician['full_name']))}}
                    </div>
                </td>
                <td>
                    <div style="width: 150px;">
                        {{ucwords(strtolower($patient['name']))}}
                    </div>
                </td>
                <td>{{$val['date']}}</td>
                <td>
                    @if($val['type'] == 3)
                        <?php
                        $seconds = Functions::hoursToSeconds($val['revisit_time_from']);
                        $newSeconds = $seconds + (10 * 60);
                        $futureTime = Functions::timeFromSeconds($newSeconds);
                        ?>
                        {{$futureTime}}
                    @else
                        {{$val['time_from']}}
                    @endif
                </td>
                <td>{{$val['time_to']}}</td>
                <td>{{$patient['phone']}}</td>
                <td>{{$patient['registration_no']}}</td>
                <td>{{$val['code']}}</td>
                <td>
                    @if($val['status'] == \core\enums\ReservationStatus::reserved)
                        Reserved
                    @elseif($val['status'] == \core\enums\ReservationStatus::accomplished)
                        Accomplished
                    @elseif($val['status'] == \core\enums\ReservationStatus::cancel)
                        <span>Cancel</span>
                    @elseif($val['status'] == \core\enums\ReservationStatus::on_progress)
                        On Progress
                    @elseif($val['status'] == \core\enums\ReservationStatus::no_show)
                        <span>No Show</span>
                    @elseif($val['status'] == \core\enums\ReservationStatus::pending)
                        <span>Pending</span>
                    @elseif($val['status'] == \core\enums\ReservationStatus::archive)
                        <span>Archive</span>
                    @endif
                </td>
                <td>
                    @if($val['patient_status'] == \core\enums\PatientStatus::waiting)
                        @if($val['patient_attend'] == 1)
                            <span>Patient Attend</span>
                        @else
                            Waiting
                        @endif
                    @elseif($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                        @if($val['patient_in_service'] == 1)
                            <span>In Service</span>
                        @elseif($val['patient_in_service'] == 3)
                            Service Done
                        @else
                            Patient In
                        @endif
                    @elseif($val['patient_status'] == \core\enums\PatientStatus::patient_out)
                        Patient Out
                    @elseif($val['patient_status'] == \core\enums\PatientStatus::cancel)
                        <span>Cancel</span>
                    @elseif($val['patient_status'] == \core\enums\PatientStatus::no_show)
                        <span>No Show</span>
                    @elseif($val['patient_status'] == \core\enums\PatientStatus::pending)
                        <span>Pending</span>
                    @elseif($val['patient_status'] == \core\enums\PatientStatus::archive)
                        <span>Archive</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

