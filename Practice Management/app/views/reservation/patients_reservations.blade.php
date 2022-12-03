<table class="table table-bordered">
    <thead>
    <tr>
        <th>Options</th>
        <th>Type</th>
        <th>Reservation Code</th>
        <th>Patient ID</th>
        <th>Clinic Name</th>
        <th>Physician Name</th>
        <th>Patient Name</th>
        <th>Phone</th>
        <th>Reservation Date</th>
        <th>Time Form</th>
        <th>Time To</th>
        <th>Reservation Status</th>
        <th>Patient Status</th>
        <th>Notes</th>
        <th>Cancel Notes</th>
        <th>Exception Reason</th>
    </tr>
    </thead>
    <tbody>
    @foreach($reservations as $key => $val)
        <tr>
            <td>
                <div class="btn-group" style="width: 100px !important;">
                    @if($val['date'] < date('Y-m-d'))
                    @else
                        @if($val['type'] != 3)
                            @if($val['status'] == \core\enums\ReservationStatus::reserved)
                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('reservation.edit'))
                                    <a reservation_id="{{$val['id']}}" title="Edit"
                                       class="btn btn-sm btn-default editReserveBtn"><i
                                                class="fa fa-pencil"></i></a>
                                @endif
                            @endif
                        @endif
                        @if($val['patient_status'] == \core\enums\PatientStatus::waiting
                                || $val['patient_status'] == \core\enums\PatientStatus::pending)
                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('reservation.cancel'))
                                <a ref_id="{{$val['id']}}" title="Cancel"
                                   class="btn btn-sm btn-danger deleteReserveBtn"><i
                                            class="fa fa-times"></i></a>
                            @endif
                        @endif
                    @endif
                </div>
            </td>
            <td>
                <div style="width: 80px;">
                    @if($val['type'] == 1)
                        Booked Visit
                    @elseif($val['type'] == 2)
                        Walk In Visit
                    @elseif($val['type'] == 3)
                        Revisit
                    @endif
                </div>
            </td>
            <td>{{$val['code']}}</td>
            <td>{{$val['patient_id']}}</td>
            <td>
                {{$val['clinic_name']}}
            </td>
            <td>
                <div style="width: 200px;">
                    {{$val['physician_name']}}
                </div>
            </td>
            <td>
                <div style="width: 200px;">
                    {{$val['patient_name']}}
                </div>
            </td>
            <td>
                {{$val['patient_phone']}}
            </td>
            <td>
                {{$val['date']}}
            </td>
            <td>
                @if($val['type'] == 3)
                    {{$val['revisit_time_from']}}
                @else
                    {{$val['time_from']}}
                @endif
            </td>
            <td>
                {{$val['time_to']}}
            </td>
            <td>
                @if($val['date'] < date('Y-m-d') && $val['status'] == \core\enums\ReservationStatus::reserved)
                    <span style="color: red">No Show</span>
                @else
                    @if($val['status'] == \core\enums\ReservationStatus::reserved)
                        <span style="color: red">Reserved</span>
                    @elseif($val['status'] == \core\enums\ReservationStatus::on_progress)
                        <span style="color: #FFA200">On Progress</span>
                    @elseif($val['status'] == \core\enums\ReservationStatus::accomplished)
                        <span style="color: #9C9B9A">Accomplished</span>
                    @elseif($val['status'] == \core\enums\ReservationStatus::cancel)
                        <span style="color: red">Cancel</span>
                    @elseif($val['status'] == \core\enums\ReservationStatus::no_show)
                        <span style="color: red">No Show</span>
                    @elseif($val['status'] == \core\enums\ReservationStatus::pending)
                        <span style="color: red">Pending</span>
                    @elseif($val['status'] == \core\enums\ReservationStatus::not_available)
                        <span style="color: red">Not Available</span>
                    @endif
                @endif
            </td>
            <td>
                @if($val['date'] < date('Y-m-d') && $val['patient_status'] == \core\enums\PatientStatus::waiting)
                    <span style="color: red">No Show</span>
                @else
                    @if($val['patient_status'] == \core\enums\PatientStatus::waiting)
                        <span style="color: red">Waiting</span>
                    @elseif($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                        <span style="color: #FFA200">Patient In</span>
                    @elseif($val['patient_status'] == \core\enums\PatientStatus::patient_out)
                        <span style="color: #9C9B9A">Patient Out</span>
                    @elseif($val['patient_status'] == \core\enums\PatientStatus::cancel)
                        <span style="color: red">Cancel</span>
                    @elseif($val['patient_status'] == \core\enums\PatientStatus::no_show)
                        <span style="color: red">No Show</span>
                    @elseif($val['patient_status'] == \core\enums\PatientStatus::pending)
                        <span style="color: red">Pending</span>
                    @elseif($val['patient_status'] == \core\enums\PatientStatus::not_available)
                        <span style="color: red">Not Available</span>
                    @endif
                @endif
            </td>
            <td>
                {{$val['notes']}}
            </td>
            <td>
                {{$val['cancel_notes']}}
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
{{$reservations->links()}}
