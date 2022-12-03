@extends('layout/main')

@section('title')
    - Complaints
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

        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            List Complaints
        </h1>
    </section>

    <section class="content">
        <div class="row">
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('complain.add'))
                <div class="col-md-2">
                    <a href="{{route('addComplain')}}">
                        <button class="btn btn-block btn-default">Add Complaint</button>
                    </a>
                    <br>
                </div>
            @endif
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
                            <label>Departments</label>
                            <br>
                            <select name="department_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($departments as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('department_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Status</label>
                            <br>
                            <select name="read" class="form-control select2">
                                <option value="">Choose</option>
                                <option value="1" @if(Input::get('read') == 1)
                                selected @endif>Read
                                </option>
                                <option value="2" @if(Input::get('read') == 2)
                                selected @endif>Not Read
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('listComplain')}}" class="btn btn-default">Clear</a>
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
                                    <th>Patient Name</th>
                                    <th>Department Name</th>
                                    <th>Created By</th>
                                    <th>Read?</th>
                                    <th>Notes</th>
                                    <th style="width: 150px">Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($complains as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td><div style="width: 200px;">{{$val['patient_name']}}</div></td>
                                        <td>{{$val['department_name']}}</td>
                                        <td><div style="width: 200px;">{{$val['create_by']}}</div></td>
                                        <td>{{$val['read'] == '1' ? 'Read' : 'Not Read'}}</td>
                                        <td>{{$val['notes']}}</td>
                                        <td>
                                            <div style="width: 190px;">
                                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('complain.edit'))
                                                @if($c_user->user_type_id == 1 || $val['created_by'] == $c_user->id)
                                                    <a class="btn btn-sm btn-warning"
                                                       href="{{route('editComplain', $val['id'])}}">Edit</a>
                                                @endif
                                            @endif
                                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('complain.delete'))
                                                @if($c_user->user_type_id == 1 || $val['created_by'] == $c_user->id)
                                                    <a class="btn btn-sm btn-danger"
                                                       href="{{route('deleteComplain', $val['id'])}}">Delete</a>
                                                @endif
                                            @endif
                                            @if($val['read'] != '1')
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('complain.read'))
                                                    <a class="btn btn-sm btn-info"
                                                       href="{{route('readComplain', $val['id'])}}">Read</a>
                                                @endif
                                            @endif
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
        </div>
    </section>
@stop