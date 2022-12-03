@extends('layout/main')

@section('title')
    - Manage Reservations
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
            "autoWidth": true
        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Manage Reservations
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
                            <label>Clinic Name</label>
                            <input type="text" name="name" value="{{Input::get('name')}}"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a class="btn btn-default" href="{{route('reservationManage')}}">Clear</a>
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
                                    <th>Clinic Name</th>
                                    <th>Status</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($clinics as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td>{{$val['name']}}</td>
                                        <td>
                                            @if($val['status'] == 1)
                                                <span style="color:green">Opened</span>
                                            @else
                                                <span style="color:red">Closed</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('manageReservation.open_close'))
                                                <div class="btn-group" style="width: 150px;">
                                                    <a class="btn btn-default"
                                                       href="{{route('manageClinic', $val['id'])}}">
                                                        @if($val['status'] == 1) Close @else Open @endif
                                                    </a>
                                                    @endif
                                                    @if($val['status'] == 1)

                                                        <a class="btn btn-default" href="{{route('manageClinicReservations')}}?hospital_id={{$val['hospital_id']}}&clinic_id={{$val['id']}}&date_from={{date('Y-m-d')}}&date_to={{date('Y-m-d')}}">
                                                            Manage
                                                        </a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$clinics->appends(Input::except('_token'))->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop