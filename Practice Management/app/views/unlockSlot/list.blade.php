@extends('layout/main')

@section('title')
    - Unlock Slots
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
@stop


@section('footer')
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script>

        $(".ask-me").click(function (e) {
            e.preventDefault();
            if (confirm('Are You Sure?')) {
                window.location.replace($(this).attr('href'));
            }
        });

        $('.datepicker').datepicker({
            startDate: "1d",
            todayHighlight: true,
            autoclose: true
        });
        $('.datepicker2').datepicker({
            todayHighlight: true,
            autoclose: true
        });

        $('#example1').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true
        });
        $(function () {
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
            })
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            List UnlockSlots
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
                            <select required id="selectPhysician2" name="physician_id" class="form-control select2">
                                <option value="">Choose</option>

                            </select>
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
                        <a href="{{route('listUnlockSlot')}}" class="btn btn-default">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered" id="example1">
                            <thead>
                            <tr>
                                <th style="width: 15px">#</th>
                                <th>Physician Name</th>
                                <th>Date</th>
                                <th>Time From</th>
                                <th>Time To</th>
                                <th>Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($unlockSlots as $val)
                                <tr>
                                    <td>{{$val['id']}}</td>
                                    <td>{{$val['physician_name']}}</td>
                                    <td>{{$val['from_time']}}</td>
                                    <td>{{$val['to_time']}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default" type="button">Action</button>
                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"
                                                    type="button">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('listUnlockSlot.edit'))
                                                    <li><a href="{{route('editUnlockSlot', $val['id'])}}">Edit</a>
                                                    </li>
                                                @endif
                                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('listUnlockSlot.delete'))
                                                        <li><a href="{{route('deleteUnlockSlot', $val['id'])}}">Delete</a>
                                                        </li>
                                                    @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop