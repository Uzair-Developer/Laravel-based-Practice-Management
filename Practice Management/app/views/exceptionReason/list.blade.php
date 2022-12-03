@extends('layout/main')

@section('title')
    - Exception Reasons
@stop

@section('content')
    <section class="content-header">
        <h1>
            List Exception Reasons
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <a href="{{route('addExceptionReason')}}">
                    <button class="btn btn-block btn-default">Add Exception Reason</button>
                </a>
                <br>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example1" class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 15px">#</th>
                                <th>Name</th>
                                <th>Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($exceptionReasons as $val)
                                <tr>
                                    <td>{{$val['id']}}</td>
                                    <td>{{$val['name']}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default" type="button">Action</button>
                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"
                                                    type="button">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                <li><a href="{{route('editExceptionReason', $val['id'])}}">Edit</a></li>
                                                <li><a href="{{route('deleteExceptionReason', $val['id'])}}">Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if(empty($exceptionReasons))
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
            </div>
        </div>
    </section>
@stop