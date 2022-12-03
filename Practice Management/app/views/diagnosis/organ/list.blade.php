@extends('layout/main')

@section('title')
    - Organs
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@stop

@section('content')
    <section class="content-header">
        <h1>
            List Organs
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <a href="{{route('addOrgan')}}">
                    <button class="btn btn-block btn-default">Add Organ</button>
                </a>
                <br>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered" id="example1">
                            <thead>
                            <tr>
                                <th style="width: 15px">#</th>
                                <th>Name</th>
                                <th>Ref Id</th>
                                <th>Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($organs as $val)
                                <tr>
                                    <td>{{$val['id']}}</td>
                                    <td>{{$val['name']}}</td>
                                    <td>{{$val['ref_id']}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default" type="button">Action</button>
                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"
                                                    type="button">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                <li><a href="{{route('editOrgan', $val['id'])}}">Edit</a></li>
                                                <li><a href="{{route('deleteOrgan', $val['id'])}}">Delete</a>
                                                </li>
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

@section('footer')
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(function () {
            $(".ask-me").click(function (e) {
                e.preventDefault();
                if (confirm('Confirm Delete?')) {
                    window.location.replace($(this).attr('href'));
                }
            });
            $('#example1').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "autoWidth": true
            });
        });

    </script>
@stop