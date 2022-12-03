@extends('layout/main')

@section('title')
    - Reservations History
@stop


@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/autocomplete/jquery.autocomplete.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/autocomplete/jquery.autocomplete.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('#example1').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "autoWidth": true,
                //"order": [[4, "desc"]],
                "sScrollY": "400px",
                "sScrollX": "100%",
                "sScrollXInner": "250%",
                "bScrollCollapse": true
            });
            $('.datepicker2').datepicker({
                todayHighlight: true,
                autoclose: true
            });
            $('.datepicker').datepicker({
                startDate: "1d",
                todayHighlight: true,
                autoclose: true
            });

            $(".viewHistoryBtn").click(function (e) {
                var reservation_id = $(this).attr('reservation_id');
                $.ajax({
                    url: '{{route('reservationViewHistory')}}',
                    method: 'POST',
                    data: {
                        reservation_id: reservation_id
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#viewHistoryBody").html(data);
                        $("#viewHistoryModal").modal('show');
                    }
                });
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
            $("#selectClinic2").attr('disabled', 'disabled');
            $.ajax({
                url: '{{route('getClinicsByHospitalId')}}',
                method: 'POST',
                data: {
                    hospital_id: '{{Input::get('hospital_id')}}'
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#selectClinic2").removeAttr('disabled').html(data).select2();
                }
            });
            @endif
        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Reservations History
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        Search
                        <button type="button" class="btn btn-box-tool pull-right"
                                data-widget="collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                    <!-- /.box-header -->
                    {{Form::open(array('role'=>"form",'method' => 'GET'))}}
                    <div class="box-body">
                        <div class="form-group col-md-3">
                            <label>Hospital</label>
                            <select autocomplete="off" id="selectHospital2" name="hospital_id"
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
                            <select autocomplete="off" id="selectClinic2" name="clinic_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Physician Name</label>
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
                            <label>Date From</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('date_from')}}"
                                   name="date_from" class="form-control datepicker2">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Date To</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('date_to')}}"
                                   name="date_to" class="form-control datepicker2">
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('reservationHistory')}}?hospital_id={{Input::get('hospital_id')}}&clinic_id={{Input::get('clinic_id')}}&date_from={{date('Y-m-d')}}&date_to={{date('Y-m-d')}}"
                           class="btn btn-info">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="col-md-10" style="margin: 10px 0;">
                            <button class="btn" style="background: #84e184">Patient Attend</button>
                            <button class="btn" style="background: #32cd32">Patient In</button>
                            <button class="btn" style="background: deepskyblue">Patient Out</button>
                            <button class="btn" style="background: #ff8566">Cancel, NoShow, Archive</button>
                            <button class="btn" style="background: #ffb84d">Pending</button>
                            <button class="btn" style="background: #68ffec">In Service</button>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Reservations: {{$reservationsCount}}
                        </div>
                        <button type="button" class="btn btn-box-tool pull-right"
                                data-widget="collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
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
                                <th>Notes</th>
                                <th>Exception Reason</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(Input::get('hospital_id'))
                                @foreach($reservations as $key => $val)
                                    <?php
                                    $physician = User::getById($val['physician_id']);
                                    $patient = Patient::getById($val['patient_id']);
                                    $count_revisit = Reservation::countRevisitOfReservation($val['id'])
                                    ?>
                                    <tr style="
                                    @if($val['patient_attend'] == 1)
                                            background:#84e184;
                                    @endif
                                    @if($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                                    @if($val['patient_in_service'] != 2)
                                            background:#68ffec;
                                    @else
                                            background:#32cd32;
                                    @endif
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
                                    @endif">
                                        <td>
                                            <a reservation_id="{{$val['id']}}"
                                               title="View History"
                                               class="btn btn-info viewHistoryBtn"><i
                                                        class="fa fa-eye"></i></a>
                                        </td>
                                        <td>
                                            @if($val['type'] == 1)
                                                Call
                                            @elseif($val['type'] == 2)
                                                Waiting
                                            @elseif($val['type'] == 3)
                                                Revisit
                                            @endif
                                        </td>
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
                                        @if($count_revisit == 0)
                                            <td>
                                                {{$count_revisit}}
                                            </td>
                                        @else
                                            <td class="getRevisitReservation"
                                                reservation_id="{{$val['id']}}"
                                                style="text-decoration:underline;color:blue;cursor: pointer;">{{$count_revisit}}</td>
                                        @endif
                                        <td>{{$val['notes']}}</td>
                                        <td>
                                            @if($val['patient_status'] == \core\enums\PatientStatus::pending)
                                                {{$val['exception_reason']}}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        {{$reservations->appends(Input::except('_token'))->links()}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="viewHistoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">View History</h4>
                </div>
                <div class="modal-body col-md-12" id="viewHistoryBody">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop
