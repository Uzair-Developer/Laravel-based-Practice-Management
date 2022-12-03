@extends('layout/main')

@section('title')
    - Public Holidays
@stop

@section('content')
    <section class="content-header">
        <h1>
            List Public Holidays
        </h1>
    </section>

    <section class="content">
        <div class="row">
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('publicHoliday.add'))
                <div class="col-md-2">
                    <a href="{{route('addPublicHoliday')}}">
                        <button class="btn btn-block btn-default">Add Public Holiday</button>
                    </a>
                    <br>
                </div>
            @endif
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <th style="width: 15px">#</th>
                                    <th>Name</th>
                                    <th>Hospital Name</th>
                                    <th>Date From</th>
                                    <th>Date To</th>
                                    <th>Active?</th>
                                    <th>Options</th>
                                </tr>
                                @foreach($publicHolidays as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td>{{$val['name']}}</td>
                                        <td>{{$val['hospital_name']}}</td>
                                        <td>{{$val['from_date']}}</td>
                                        <td>{{$val['to_date']}}</td>
                                        <td>{{$val['status'] == 1 ? 'Yes' : 'No'}}</td>
                                        <td>
                                            <div class="btn-group" style="width: 150px;">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('publicHoliday.edit'))
                                                    <a class="btn btn-default" href="{{route('editPublicHoliday', $val['id'])}}">Edit</a>

                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('publicHoliday.delete'))

                                                    <a class="btn btn-danger" href="{{route('deletePublicHoliday', $val['id'])}}">Delete</a>

                                                @endif


                                                <a class="btn btn-default" href="{{route('changeStatusPublicHoliday', $val['id'])}}">
                                                    {{$val['status'] == 1 ? 'Deactivate' : 'Activate'}}
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if(empty($publicHolidays))
                                    <tr>
                                        <td colspan="7">
                                            <center>No Records!</center>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    </section>
@stop