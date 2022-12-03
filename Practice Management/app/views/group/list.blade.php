@extends('layout/main')

@section('title')
    - Groups
@stop

@section('content')
    <section class="content-header">
        <h1>
            List Groups
        </h1>
    </section>

    <section class="content">
        <div class="row">
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('permissions.add'))
            <div class="col-md-2">
                <a href="{{route('addGroup')}}">
                    <button class="btn btn-block btn-default">Add Group</button>
                </a>
                <br>
            </div>
            @endif
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <th style="width: 15px">#</th>
                                <th>Group Name</th>
                                <th>System</th>
                                <th>In Res Filter</th>
                                <th>Options</th>
                            </tr>
                            @foreach($groups as $val)
                                <tr>
                                    <td>{{$val['id']}}</td>
                                    <td>{{$val['name']}}</td>
                                    <td>{{$val['system'] ? 'Yes' : 'No'}}</td>
                                    <td>{{$val['in_filter'] == 1 ? 'Yes' : 'No'}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default" type="button">Action</button>
                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"
                                                    type="button">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('permissions.edit'))
                                                    <li><a href="{{route('editGroup', $val['id'])}}">Edit</a></li>
                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('permissions.delete'))
                                                    <li><a class="ask-me"
                                                           href="{{route('deleteGroup', $val['id'])}}">Delete</a></li>
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

@section('footer')
    <script>
        $(function () {
            $(".ask-me").click(function (e) {
                e.preventDefault();
                if (confirm('Are You Sure?')) {
                    window.location.replace($(this).attr('href'));
                }
            });
        })
    </script>
@stop