@extends('layout/main')

@section('title')
    - Clinics
@stop

@section('content')
    <section class="content-header">
        <h1>
            List Clinics
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
                            <select name="hospital_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Name</label>
                            <input type="text" name="name" value="{{Input::get('name')}}" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Code</label>
                            <input type="text" name="code" value="{{Input::get('code')}}" class="form-control">
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a class="btn btn-default" href="{{route('clinics')}}">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('clinic_pms.add'))
                <div class="col-md-2">
                    <a href="{{route('addClinic')}}">
                        <button class="btn btn-block btn-default">Add Clinic</button>
                    </a>
                </div>
            @endif
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('clinic_pms.printExcel'))
                <div class="col-md-2">
                    {{Form::open(array('role'=>"form", 'route' => 'printExcelClinics'))}}
                    @if(Input::except('_token'))
                        @foreach(Input::except('_token') as $key => $val)
                            <input type="hidden" name="{{$key}}" value="{{$val}}">
                        @endforeach
                    @endif
                    <button class="btn btn-primary" type="submit">Download Excel</button>
                    {{Form::close()}}
                </div>
            @endif
            <div class="col-md-12" style="margin-top: 10px;">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <th style="width: 15px">#</th>
                                <th>Hospital Name</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Options</th>
                            </tr>
                            @foreach($clinics as $clinic)
                                <tr>
                                    <td>{{$clinic['id']}}</td>
                                    <td>{{$clinic['hospital_name']}}</td>
                                    <td>{{$clinic['name']}}</td>
                                    <td>{{$clinic['code']}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default" type="button">Action</button>
                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"
                                                    type="button">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('clinic_pms.edit'))
                                                    <li><a href="{{route('editClinic', $clinic['id'])}}">Edit</a></li>
                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('clinic_pms.delete'))
                                                    <li><a href="{{route('deleteClinic', $clinic['id'])}}">Delete</a>
                                                    </li>
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