<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>#</th>
        <th>Clinic Name</th>
        <th>Physician Name</th>
        <th>Patient Name</th>
        <th>Patient Phone</th>
        <th>Patient ID</th>
        <th>Date</th>
        <th>Time From</th>
        <th>Time To</th>
        <th>Reservation Status</th>
        <th>Patient Status</th>
        <th>Res Type</th>
        <th>Duration</th>
        <th>Revisits Count</th>
        @if($c_user->user_type_id == 1 || $c_user->hasAccess('supervisor.access'))
            <th>Create By</th>
            <th>Create At</th>
            <th>Update By</th>
            <th>Update At</th>
        @endif
        <th>Notes</th>
        <th>Cancel Notes</th>
        <th>Cancel Reason</th>
        <th>Send Cancellation SMS</th>
        <th>Exception Reason</th>
    </tr>
    </thead>
    <tbody>
    @foreach($reservations as $val)
        <?php
        $physician = User::getById($val['physician_id']);
        $patient = Patient::getById($val['patient_id']);
        $count_revisit = Reservation::countRevisitOfReservation($val['id'])
        ?>
        <tr>
            <td>{{$val['id']}}</td>
            <td>{{Clinic::getNameById($val['clinic_id'])}}</td>
            <td>{{ucwords(strtolower($physician['full_name']))}}</td>
            <td>{{ucwords(strtolower($patient['name']))}}</td>
            <td>{{$patient['phone']}}</td>
            <td>{{$patient['registration_no']}}</td>
            <td>{{$val['date']}}</td>
            <td>{{$val['time_from']}}</td>
            <td>{{$val['time_to']}}</td>
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
                    Waiting
                @elseif($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                    Patient In
                @elseif($val['patient_status'] == \core\enums\PatientStatus::patient_out)
                    Patient Out
                @elseif($val['patient_status'] == \core\enums\PatientStatus::cancel)
                    <span>Cancel</span>
                @elseif($val['patient_status'] == \core\enums\PatientStatus::no_show)
                    @if($val['patient_attend'] == 1)
                        <span>Patient Attend</span>
                    @else
                        <span>No Show</span>
                    @endif
                @elseif($val['patient_status'] == \core\enums\PatientStatus::pending)
                    <span>Pending</span>
                @elseif($val['patient_status'] == \core\enums\PatientStatus::archive)
                    <span>Archive</span>
                @endif
            </td>
            <td>
                @if($val['type'] == 1)
                    Call Reservation
                @elseif($val['type'] == 2)
                    Walk In Reservation
                @elseif($val['type'] == 3)
                    Revisit Reservation
                @endif
            </td>
            <td>{{$val['walk_in_duration']}}</td>
            <td>{{$count_revisit}}</td>
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('supervisor.access'))
                <td>
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

                </td>
                <td>{{$val['created_at']}}</td>
                <td>
                    <?php
                    if ($val['update_by']) {
                        $oneUser = Sentry::findUserById($val['update_by']);
                        $groups = $oneUser->getGroups();
                        $count = count($groups);
                        $update_by = User::getName($val['update_by']) . ' (';
                        foreach ($groups as $index2 => $val2) {
                            if ($count == $index2 + 1) {
                                $update_by .= $val2['name'] . ')';
                            } else {
                                $update_by .= $val2['name'] . ', ';
                            }
                        }
                    } else {
                        $update_by = User::getName($val['update_by']);
                    }
                    ?>
                    {{$update_by}}
                </td>
                <td>{{$val['updated_at']}}</td>
            @endif
            <td>{{$val['notes']}}</td>
            <td>{{$val['cancel_notes']}}</td>
            <td>{{AttributePms::getName($val['cancel_reason_id'])}}</td>
            <td>
                @if($val['send_cancel_sms'] == 1)
                    Yes
                @elseif($val['send_cancel_sms'] == 2)
                    No
                @endif
            </td>
            <td>
                @if($val['patient_status'] == \core\enums\PatientStatus::pending)
                    {{$val['exception_reason']}}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
