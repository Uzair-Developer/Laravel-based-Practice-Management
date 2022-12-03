@extends('layout/main')

@section('title')
    - Symptom Comments
@stop

@section('footer')
    <script>
        $(".select2").select2();
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            List Symptom Comments
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        Filters
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        {{Form::open(array('role'=>"form", 'method' => 'GET'))}}
                        <div class="form-group col-md-4">
                            <input placeholder="Symptom Name" type="text" value="{{Input::get('symptom')}}"
                                   name="symptom" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <select name="user" class="form-control select2">
                                <option value="">User Name</option>
                                @foreach($users as $val)
                                    <option @if(Input::get('user') == $val['id']) selected
                                            @endif value="{{$val['id']}}">{{$val['user_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                        {{Form::close()}}
                    </div>

                </div>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <th style="width: 15px">#</th>
                                <th>Symptom Name</th>
                                <th>User Name</th>
                                <th>Status</th>
                                <th>Comment</th>
                                <th>Actions</th>
                            </tr>
                            @foreach($data as $val)
                                <tr>
                                    <td>{{$val['id']}}</td>
                                    <td>{{$val['symptom_name']}}</td>
                                    <td>{{$val['user_name']}}</td>
                                    <td>
                                        @if($val['status'] == 1)
                                            New
                                        @elseif($val['status'] == 2)
                                            Pending
                                        @elseif($val['status'] == 3)
                                            Read
                                        @endif
                                    </td>
                                    <td>{{$val['comment']}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default" type="button">Action</button>
                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"
                                                    type="button">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                <li><a href="{{route('statusComment', array($val['id'], 'read'))}}">Read</a></li>
                                                <li><a href="{{route('statusComment', array($val['id'], 'pending'))}}">Pending</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if(empty($data->toArray()['data']))
                                <td colspan="7">
                                    <center>No Records!</center>
                                </td>
                            @endif
                            </tbody>
                        </table>
                        {{$data->appends(Input::except('_token'))->links()}}
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop