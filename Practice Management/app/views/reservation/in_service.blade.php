@extends('layout/main')

@section('title')
    - In Service Reservations
@stop


@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#example1').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[6, 'asc']],
            "sScrollY": "400",
            "sScrollX": "100%",
            "sScrollXInner": "250%",
            "bScrollCollapse": true
        });
        $(function () {
            $(".ask-me").click(function (e) {
                e.preventDefault();
                if (confirm('Are You Sure?')) {
                    window.location.replace($(this).attr('href'));
                }
            });


            $("#selectHospital2").change(function (e) {
                $("#selectClinic2").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getClinicsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectClinic2").removeAttr('disabled').html(data).select2();
                        $("#patientName").autocomplete("destroy");
                        patientNameautocomplete('?hospital_id=' + $("#selectHospital2").val());
                        idBlur();
                    }
                });
            });
            $("#selectClinic2").change(function (e) {
                $("#selectPhysician2").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPhysicianByClinicId')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectPhysician2").removeAttr('disabled').html(data).select2();
                    }
                });
            });

            @if(Input::get('hospital_id'))
              $.ajax({
                        url: '{{route('getClinicsByHospitalId')}}',
                        method: 'POST',
                        data: {
                            hospital_id: $("#selectHospital2").val()
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#selectClinic2").removeAttr('disabled').html(data).select2();
                            @if(Input::get('clinic_id'))
                            $("#selectClinic2").val('{{Input::get('clinic_id')}}').select2();
                            $.ajax({
                                url: '{{route('getPhysicianByClinicId')}}',
                                method: 'POST',
                                data: {
                                    clinic_id: $('#selectClinic2').val()
                                },
                                headers: {token: '{{csrf_token()}}'},
                                success: function (data) {
                                    $("#selectPhysician2").removeAttr('disabled').html(data).select2();
                                    @if(Input::get('physician_id'))
                                    $("#selectPhysician2").val('{{Input::get('physician_id')}}').select2();
                                    @endif


                                }
                            });
                            @endif


                        }
                    });
            @endif


        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            In Service Reservations
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    <div class="box-header">
                        Search
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    {{Form::open(array('role'=>"form", 'method' => 'GET'))}}
                    <div class="box-body">
                        <div class="form-group col-md-3">
                            <label>Hospital *</label>
                            <br>
                            <select required autocomplete="off" id="selectHospital2" name="hospital_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Clinic</label>
                            <br>
                            <select autocomplete="off" id="selectClinic2" name="clinic_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Physician Name</label>
                            <br>
                            <select id="selectPhysician2" name="physician_id" class="form-control select2">
                                <option value="">Choose</option>

                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Reservation Code</label>
                            <input type="text" name="code" value="{{Input::get('code')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Id</label>
                            <input type="text" name="id" value="{{Input::get('id')}}" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Phone</label>
                            <input type="text" maxlength="15" name="phone" value="{{Input::get('phone')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Name</label>
                            <input type="text" name="name" value="{{Input::get('name')}}" class="form-control">
                        </div>

                        <div class="form-group col-md-3">
                            <label>National Id</label>
                            <input type="text" name="national_id" value="{{Input::get('national_id')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Reservation Type</label>
                            <br>
                            <select autocomplete="off" name="type"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                <option @if(Input::get('type') == 1) selected @endif value="1">
                                    By Call
                                </option>
                                <option @if(Input::get('type') == 2) selected @endif value="2">
                                    Wait
                                </option>
                                <option @if(Input::get('type') == 3) selected @endif value="3">
                                    Revisit
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a class="btn btn-default" href="{{route('inServiceReservations')}}">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if(Input::get('hospital_id'))
                <div class="col-md-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-bordered" id="example1">
                                <thead>
                                <tr>
                                    <th>Options</th>
                                    <th>Type</th>
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
                                    <th>Revisits Count</th>
                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('supervisor.access'))
                                        <th>Create By</th>
                                        <th>Create At</th>
                                        <th>Update By</th>
                                        <th>Update At</th>
                                    @endif
                                    <th>Notes</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($inService as $val)
                                    <?php
                                    $physician = User::getById($val['physician_id']);
                                    $patient = Patient::getById($val['patient_id']);
                                    $count_revisit = Reservation::countRevisitOfReservation($val['id'])
                                    ?>
                                    <tr>
                                        <td>
                                            @if($val['patient_status'] == \core\enums\PatientStatus::patient_in
                                            && $val['patient_in_service'] == 1
                                            && ($c_user->hasAccess('in_service.service_done') || $c_user->user_type_id == 1))
                                                <a class="btn btn-default bg-green ask-me" title="Service Done"
                                                   href="{{route('patientOutOrPatientInService') . '?reservation_id=' . $val['id'] . '&type=service_done'}}">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($val['type'] == 1)
                                                By Call
                                            @elseif($val['type'] == 2)
                                                Wait
                                            @elseif($val['type'] == 3)
                                                Revisit
                                            @endif
                                        </td>
                                        <td>{{Clinic::getNameById($val['clinic_id'])}}</td>
                                        <td>
                                            <div>
                                                {{ucwords(strtolower($physician['full_name']))}}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                {{ucwords(strtolower($patient['name']))}}
                                            </div>
                                        </td>
                                        <td>{{$val['date']}}</td>
                                        <td>
                                            @if($val['type'] == 1)
                                                {{$val['time_from']}}
                                            @elseif($val['type'] == 3)
                                                <?php
                                                $currentTime = strtotime($val['revisit_time_from']);
                                                $futureTime = $currentTime + (60 * 5);
                                                ?>
                                                {{date("H:i:s", $futureTime)}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($val['type'] == 1)
                                                {{$val['time_to']}}
                                            @endif
                                        </td>
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
                                        @if($count_revisit == 0)
                                            <td>
                                                {{$count_revisit}}
                                            </td>
                                        @else
                                            <td class="getRevisitReservation"
                                                reservation_id="{{$val['id']}}"
                                                style="text-decoration:underline;color:blue;cursor: pointer;">{{$count_revisit}}</td>
                                        @endif
                                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('supervisor.access'))
                                            <td>
                                                <div>
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
                                                <div>
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
                                                </div>
                                            </td>
                                            <td>{{$val['updated_at']}}</td>
                                        @endif
                                        <td>{{$val['notes']}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@stop