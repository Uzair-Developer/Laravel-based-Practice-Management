@extends('layout/main')

@section('title')
    - Ip To Printer
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(function () {
            $(".ask-me").click(function (e) {
                e.preventDefault();
                if (confirm('Are You Sure?')) {
                    window.location.replace($(this).attr('href'));
                }
            });

            $('#example1').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                'order': [[0, 'asc']],
                "sScrollY": "400px",
                "sScrollX": "100%",
                "sScrollXInner": "100%",
                "bScrollCollapse": true
            });
        });
    </script>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Ip To Printer
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
                    {{Form::open(array('method' => 'GET'))}}
                    <div class="box-body">
                        <div class="form-group col-md-3">
                            <label>Hospital</label>
                            <select autocomplete="off" id="selectHospital2" name="hospital_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Printer IP</label>
                            <input type="text" name="ip" value="{{Input::get('ip')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Printer Name</label>
                            <input name="name" value="{{Input::get('name')}}"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('ipToPrinter')}}" class="btn btn-info">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('ipToPrinter.add'))
                <div class="col-md-2">
                    <a href="{{route('addIpToPrinter')}}">
                        <button class="btn btn-block btn-default">Add Ip To Printer</button>
                    </a>
                    <br>
                </div>
            @endif
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered" id="example1">
                            <thead>
                            <tr>
                                <th style="width: 15px">#</th>
                                <th>Hospital</th>
                                <th>Printer IP</th>
                                <th>Printer Name</th>
                                <th>Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($ipToPrinter as $val)
                                <tr>
                                    <td>{{$val['id']}}</td>
                                    <td>{{$val['hospital_name']}}</td>
                                    <td>{{$val['ip']}}</td>
                                    <td>{{$val['name']}}</td>
                                    <td>
                                        <div class="btn-group" style="width: 150px;">
                                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('ipToPrinter.edit'))
                                                <a class="btn btn-default"
                                                   href="{{route('editIpToPrinter', $val['id'])}}">Edit</a>

                                            @endif
                                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('ipToPrinter.delete'))
                                                <a class="btn btn-danger ask-me"
                                                   href="{{route('deleteIpToPrinter', $val['id'])}}">Delete</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    </section>
@stop