@extends('layout/main')

@section('title')
    - Patient Attend
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
        $('#example1').DataTable({
            "paging": false,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true
        });
    </script>
    <script>
        $(function () {
            $('.datepicker2').datepicker({
                todayHighlight: true,
                autoclose: true
            });
        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Patient Attend
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
                            <select id="selectHospital2" name="hospital_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Reservation Code</label>
                            <input type="text" name="reservation_code" value="{{Input::get('reservation_code')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Date</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('date')}}"
                                   name="date" class="form-control datepicker2">
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a class="btn btn-default" href="{{route('listPatientAttend')}}">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="example1">
                                <thead>
                                <tr>
                                    <th style="width: 15px">#</th>
                                    <th>Status</th>
                                    <th>Reservation Code</th>
                                    <th>Patient Name</th>
                                    <th>Patient Phone</th>
                                    <th>Clinic Name</th>
                                    <th>Physician Name</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($patients as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td>
                                            @if($val['status'] == 1)
                                                <span style="color: green">Attend</span>
                                            @else
                                                <span style="color: red">Not Attend</span>
                                            @endif
                                        </td>
                                        <td>{{$val['reservation_code']}}</td>
                                        <td>{{$val['patient_name']}}</td>
                                        <td>{{$val['patient_phone']}}</td>
                                        <td>{{$val['clinic_name']}}</td>
                                        <td>{{$val['physician_name']}}</td>
                                        <td>{{$val['created_by']}}</td>
                                        <td>{{$val['created_at']}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @if (isset($inputs['hospital_id']) && $inputs['hospital_id'])
                                {{$patients->appends(Input::except('_token'))->links()}}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop