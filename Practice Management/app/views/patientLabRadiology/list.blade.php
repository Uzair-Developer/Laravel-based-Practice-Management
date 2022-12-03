@extends('layout/main')

@section('title')
    - Lab Results
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/loading_mask/waitMe.css')}}">
@stop


@section('footer')
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/loading_mask/waitMe.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#example1').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [[8, "desc"]],
                "sScrollY": "400px",
                "sScrollX": "100%",
                "sScrollXInner": "150%",
                "bScrollCollapse": true
            });

            $('.datepicker2').datepicker({
                todayHighlight: true,
                autoclose: true
            });

            $('.datepic_search').datepicker({
                todayHighlight: true,
                autoclose: true
            });

            $(".ask-me").click(function (e) {
                e.preventDefault();
                if (confirm('Are You Sure?')) {
                    window.location.replace($(this).attr('href'));
                }
            });

            $(document).on('click', '.orderDetailsBtn', function (e) {
                $('#WithMe').waitMe({
                    effect: 'ios',
                    text: 'Please wait...',
                    bg: 'rgba(255,255,255,0.7)',
                    color: '#000',
                    maxSize: '',
                    source: 'img.svg'
                });
                var order = $(this).attr('orderNum');
                $.ajax({
                    url: '{{route('getPatientOrderLapRadiology')}}',
                    method: 'POST',
                    data: {
                        order_id: order
                    },
                    success: function (data) {
                        $('#WithMe').waitMe('hide');
                        $("#order_patient_name").html(data.patient_name);
                        $("#order_details").html(data.orderDetails);
                        $("#orderDetailsModal").modal('show');
                    }
                });
            });

            $(document).on('click', '.editPatientPhoneBtn', function (e) {
                var patient_id = $(this).attr('patient_id');
                var patient_phone = $(this).attr('patient_phone');
                $("#edit_patient_phone").val(patient_phone);
                $("#edit_patient_id").val(patient_id);
                $.ajax({
                    url: '{{route('getPatientData')}}',
                    method: 'POST',
                    data: {
                        patient_id: patient_id
                    },
                    success: function (data) {
                        $("#edit_patient_name").html('').html(data.name);
                        $("#edit_patient_birth_date").html('').html(data.birthday);
                        $("#edit_patient_age").html('').html(data.age);
                        $("#edit_patient_email").html('').html(data.email);
                        $("#edit_patient_address").html('').html(data.address);
                        $("#editPatientPhoneModal").modal('show');
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
            $.ajax({
                        url: '{{route('getClinicsByHospitalId')}}',
                        method: 'POST',
                        async: false,
                        data: {
                            hospital_id: '{{Input::get('hospital_id')}}'
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#selectClinic2").html(data).select2();
                            @if(Input::get('clinic_id'))
                            $("#selectClinic2").val('{{Input::get('clinic_id')}}').select2();
                            $.ajax({
                                url: '{{route('getPhysicianByClinicId')}}',
                                method: 'POST',
                                async: false,
                                data: {
                                    clinic_id: '{{Input::get('clinic_id')}}'
                                },
                                headers: {token: '{{csrf_token()}}'},
                                success: function (data) {
                                    $("#selectPhysician2").html(data).select2();
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
            Lab Results
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
                            <select autocomplete="off" id="selectClinic2" name="clinic_id" class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Physicians</label>
                            <select autocomplete="off" class="form-control select2" name="physician_id"
                                    id="selectPhysician2">
                                <option value="">Choose</option>

                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient ID</label>
                            <input type="text" name="patient_id" value="{{Input::get('patient_id')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Name</label>
                            <input type="text" name="patient_name" value="{{Input::get('patient_name')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Phone</label>
                            <input type="text" name="patient_phone" value="{{Input::get('patient_phone')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Date From</label>
                            <input type="text" data-date-format="yyyy-mm-dd"
                                   value="{{Input::get('date_from')}}"
                                   name="date_from" class="form-control datepic_search">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Date To</label>
                            <input type="text" data-date-format="yyyy-mm-dd"
                                   value="{{Input::get('date_to')}}"
                                   name="date_to" class="form-control datepic_search">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Type</label>
                            <select class="form-control select2" name="type">
                                <option value="">Choose</option>
                                <option @if(Input::get('type') == 'Lab') selected @endif value="Lab">Lab</option>
                                <option @if(Input::get('type') == 'Radiology') selected @endif value="Radiology">
                                    Radiology
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a class="btn btn-default" href="{{route('listPatientLapRadiology')}}">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if($inputs)
                <div class="col-md-12" id="WithMe">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="">
                                <table class="table table-bordered" id="example1">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Order ID</th>
                                        <th>Patient ID</th>
                                        <th>Patient Name</th>
                                        <th>Phone</th>
                                        @if(app('production'))
                                            <th>Password</th>
                                        @endif
                                        <th>Type</th>
                                        <th>Physician Name</th>
                                        <th>Created At</th>
                                        <th>Send SMS</th>
                                        @if(app('production'))
                                            <th>SMS Response</th>
                                        @endif
                                        <th>Options</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($patientLapRadiology as $val)
                                        <tr>
                                            <td>{{$val['id']}}</td>
                                            <td>{{$val['order_id']}}</td>
                                            <td>{{$val['patient_reg_no']}}</td>
                                            <td>
                                                <div>{{$val['patient_name']}}</div>
                                            </td>
                                            <td>{{$val['patient_phone']}}</td>
                                            @if(app('production'))
                                                <td>{{$val['password']}}</td>
                                            @endif
                                            <td>{{$val['station']}}</td>
                                            <td>
                                                <div>{{$val['physician_name']}}</div>
                                            </td>
                                            <td>{{$val['datetime']}}</td>
                                            <td>
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('patientLapRadiology.on_off_lab_sms'))
                                                    @if($val['patient_lab_sms'] == 1)
                                                        <a class="btn btn-danger ask-me"
                                                           href="{{route('changeSendLabSMS', $val['patient_id'])}}">Turn OFF</a>
                                                    @else
                                                        <a class="btn btn-default ask-me"
                                                           href="{{route('changeSendLabSMS', $val['patient_id'])}}">Turn ON</a>
                                                    @endif
                                                @else
                                                    @if($val['patient_lab_sms'] == 1)
                                                        Yes
                                                    @else
                                                        <span style="color:red;">No</span>
                                                    @endif
                                                @endif
                                            </td>
                                            @if(app('production'))
                                                <td>{{$val['message_status']}}</td>
                                            @endif
                                            <td>
                                                <div class="btn-group">
                                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('patientLapRadiology.order_details'))
                                                        <a class="btn btn-default orderDetailsBtn"
                                                           orderNum="{{$val['order_id']}}">Details</a>
                                                    @endif
                                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('patientLapRadiology.reset_password'))
                                                        <a class="btn btn-warning ask-me"
                                                           href="{{route('resetPatientPassword', $val['patient_reg_no'])}}">Reset
                                                            Password</a>
                                                    @endif
                                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('patientLapRadiology.edit_phone'))
                                                        <a class="btn btn-info editPatientPhoneBtn"
                                                           patient_id="{{$val['patient_id']}}"
                                                           patient_phone="{{$val['patient_phone']}}">Edit</a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{$patientLapRadiology->appends(Input::except('_token'))->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Order Details</h4>
                </div>
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-12">
                        <label>Patient Name</label>

                        <div id="order_patient_name"></div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Details</label>

                        <div id="order_details"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPatientPhoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Edit Patient Phone</h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'editPatientPhone'))}}
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-4">
                        <label>New Patient Phone</label>
                        <input required class="form-control" type="text" name="phone" id="edit_patient_phone">
                        <input type="hidden" name="id" id="edit_patient_id">
                    </div>
                    <div class="form-group col-md-12">
                        <hr>
                        <label>Verification Info</label>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Patient Name</label>

                        <div id="edit_patient_name"></div>

                    </div>
                    <div class="form-group col-md-3">
                        <label>Birth date</label>

                        <div id="edit_patient_birth_date"></div>

                    </div>
                    <div class="form-group col-md-3">
                        <label>Age</label>

                        <div id="edit_patient_age"></div>

                    </div>
                    <div class="form-group col-md-6">
                        <label>Email</label>

                        <div id="edit_patient_email"></div>

                    </div>
                    <div class="form-group col-md-6">
                        <label>Address</label>

                        <div id="edit_patient_address"></div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
@stop