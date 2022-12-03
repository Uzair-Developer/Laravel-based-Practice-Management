@extends('layout/main')

@section('title')
    - Hospitals
@stop

@section('content')
    <section class="content-header">
        <h1>
            List Hospitals
        </h1>
    </section>

    <section class="content">
        <div class="row">
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('hospital.add'))
            <div class="col-md-2">
                <a href="{{route('addHospital')}}">
                    <button class="btn btn-block btn-default">Add Hospital</button>
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
                                <th>Name</th>
                                <th>Authority Code</th>
                                <th>Time Zone</th>
                                <th>Options</th>
                            </tr>
                            @foreach($hospitals as $hospital)
                                <tr>
                                    <td>{{$hospital['id']}}</td>
                                    <td>{{$hospital['name']}}</td>
                                    <td>{{$hospital['issue_authority_code']}}</td>
                                    <td>{{$hospital['time_zone']}}</td>
                                    <td>
                                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('hospital.edit'))
                                            <div class="btn-group">
                                                <button class="btn btn-default" type="button">Action</button>
                                                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"
                                                        type="button">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul role="menu" class="dropdown-menu">
                                                    <li><a href="{{route('editHospital', $hospital['id'])}}">Edit</a>
                                                    </li>
                                                    {{--                                                <li><a href="{{route('deleteHospital', $hospital['id'])}}">Delete</a></li>--}}
                                                </ul>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if(empty($hospitals))
                                <td colspan="7">
                                    <center>No Records!</center>
                                </td>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop