@extends('layout/main')

@section('title')
    - Users
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
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
        $(function () {

            $(".ask-me").click(function (e) {
                e.preventDefault();
                msg = $(this).attr('show-msg');
                if (msg) {
                    if (confirm('Are You Sure? (' + msg + ')')) {
                        window.location.replace($(this).attr('href'));
                    }
                } else {
                    if (confirm('Are You Sure?')) {
                        window.location.replace($(this).attr('href'));
                    }
                }
            });

            $(".openModal").click(function (e) {
                var user_id = $(this).attr('user_id');
                $("#user_id").val(user_id);
                $("#myModal").modal('show');
            })
        })
    </script>
@stop
@section('content')
    <section class="content-header">
        <h1>
            List Users
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
                            <label>Name</label>
                            <input type="text" name="name" value="{{Input::get('name')}}" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Status</label>
                            <select name="activated" class="form-control select2">
                                <option value="">Choose</option>
                                <option value="1"
                                        @if(Input::get('activated') == 1)
                                        selected @endif>Active
                                </option>
                                <option value="0"
                                        @if(Input::get('activated') === '0')
                                        selected @endif>InActive
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Role</label>
                            <select name="user_type_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($userTypes as $val)
                                    <option value="{{$val['id']}}"
                                            @if(Input::get('user_type_id') == $val['id'])
                                            selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Group Name</label>
                            <select name="group_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($groups as $val)
                                    <option value="{{$val['id']}}"
                                            @if(Input::get('group_id') == $val['id'])
                                            selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a class="btn btn-default" href="{{route('users')}}">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('user.add'))
                <div class="col-md-2">
                    <a href="{{route('addUser')}}">
                        <button class="btn btn-block btn-default">Add User</button>
                    </a>
                </div>
            @endif
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('user.printExcel'))
                <div class="col-md-2">
                    {{Form::open(array('role'=>"form", 'route' => 'printExcelUsers'))}}
                    @if(Input::except('_token'))
                        @foreach(Input::except('_token') as $key => $val)
                            <input type="hidden" name="{{$key}}" value="{{$val}}">
                        @endforeach
                    @endif
                    <button class="btn btn-primary" type="submit">Download Excel</button>
                    {{Form::close()}}
                </div>
            @endif
            @if(app('production'))
                @if($c_user->user_type_id == 1 || $c_user->hasAccess('user.his_import_physician'))
                    <div class="col-md-3">
                        <a href="{{route('hisImportPhysician')}}">
                            <button class="btn btn-block btn-default">HIS Import Physician</button>
                        </a>
                    </div>
                @endif
            @endif
            <div class="col-md-12" style="margin-top: 10px;">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="example1">
                                <thead>
                                <tr>
                                    <th style="width: 15px">#</th>
                                    <th>Full Name</th>
                                    <th>User Name</th>
                                    <th>Status</th>
                                    <th>Role Name</th>
                                    <th>Group Name</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{$user['id']}}</td>
                                        <td>
                                            <div style="width: 200px;">{{$user['full_name']}}</div>
                                        </td>
                                        <td>{{$user['user_name']}}</td>
                                        <td>
                                            @if($user['activated'] == 1)
                                                Active
                                            @else
                                                <span style="color: red">InActive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="width: 150px;">{{$user['role_name']}}</div>
                                        </td>
                                        <td>
                                            <div style="width: 200px;">{{$user['group_name']}}</div>
                                        </td>
                                        <td>
                                            <div class="btn-group" style="width:450px;">

                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('user.edit'))
                                                    <a class="btn btn-default"
                                                       href="{{route('editUser', $user['id'])}}">Edit</a>
                                                @endif
                                                {{--@if($c_user->user_type_id == 1 || $c_user->user_type_id == 2)--}}
                                                {{--<a href="{{route('addSecurity', $user['id'])}}">Permissions</a>--}}
                                                {{----}}
                                                {{--@endif--}}
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('user.changeStatus'))

                                                    @if($user['activated'] == 1)
                                                        <a @if($user['user_type_id'] == 7)
                                                           show-msg="All Reservations Will Convert To be Cancelled"
                                                           @endif
                                                           class="ask-me btn btn-default"
                                                           href="{{route('changeStatus', $user['id'])}}">
                                                            Deactivate</a>
                                                    @else
                                                        <a class="ask-me btn btn-default"
                                                           href="{{route('changeStatus', $user['id'])}}">
                                                            Activate</a>
                                                    @endif

                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('user.resetPassword'))
                                                    <a class="ask-me btn btn-default"
                                                       href="{{route('resetPassword', $user['id'])}}">Reset
                                                        Password</a>

                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('user.changePassword'))
                                                    <a style="cursor: pointer;" class="openModal btn btn-default"
                                                       user_id="{{$user['id']}}">Change Password</a>
                                                @endif
                                                {{--@if($c_user->user_type_id == 1 || $c_user->hasAccess('user.delete'))--}}
                                                {{--<a class="ask-me"--}}
                                                {{--href="{{route('deleteUser', $user['id'])}}">Delete</a>--}}
                                                {{--@endif--}}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$links}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Change Password</h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'changePassword'))}}
                <div class="modal-body">
                    <div class="form-group">
                        <label>Password</label>
                        <input required name="password" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label>Password Confirmation</label>
                        <input required name="password_confirmation" class="form-control"/>
                        <input name="user_id" id="user_id" type="hidden"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
    <br>
    <br>
    <br>
@stop