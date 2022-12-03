@extends('layout/main')

@section('title')
    - AgentComments
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@stop

@section('content')
    <section class="content-header">
        <h1>
            List AgentComments
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="example1">
                                <thead>
                                <tr>
                                    <th style="width: 15px">#</th>
                                    <th>Created By</th>
                                    <th>Read</th>
                                    <th>Date</th>
                                    <th>Notes</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($agentComments as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td><div style="width: 200px;">{{$val['create_name']}}</div></td>
                                        <td>{{$val['seen'] ? 'Yes' : 'No'}}</td>
                                        <td><div style="width: 150px;">{{$val['created_at']}}</div></td>
                                        <td>{{$val['notes']}}</td>
                                        <td>
                                            <div class="btn-group" style="width: 150px;">
                                                @if(!$val['seen'])
                                                    <a class="btn btn-default"
                                                       href="{{route('readAgentComment', $val['id'])}}">Read</a>

                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('agentComment.delete'))
                                                    @if($c_user->user_type_id == 1 || $val['create_by'] == $c_user->id)

                                                        <a class="btn btn-default"
                                                           href="{{route('deleteAgentComment', $val['id'])}}">Delete</a>

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