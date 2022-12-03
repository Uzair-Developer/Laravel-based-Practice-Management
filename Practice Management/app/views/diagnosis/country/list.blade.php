@extends('layout/main')

@section('title')
    - Countries
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/autocomplete/jquery.autocomplete.css')}}">
@stop
@section('footer')
    <script src="{{asset('plugins/autocomplete/jquery.autocomplete.js')}}"></script>
    <script>
        $("#QC_search").autocomplete({
            url: '{{route('autoCompleteCountry')}}',
//        mustMatch: true,
            maxItemsToShow: 20,
            selectFirst: false,
            autoFill: false,
            selectOnly: true,
            remoteDataType: 'json'
        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            List Countries
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <a href="{{route('addCountry')}}">
                    <button class="btn btn-block btn-default">Add Country</button>
                </a>
                <br>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        Filters
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body" style="">
                        {{Form::open(array('role'=>"form", 'method' => 'GET'))}}
                        <div class="form-group col-md-6">
                            <input id="QC_search" placeholder="Name" type="text" value="{{Input::get('q')}}" name="q"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-6">
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
                        <table id="example1" class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 15px">#</th>
                                <th>Name</th>
                                <th>Parent name</th>
                                <th>Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($countries as $val)
                                <tr>
                                    <td>{{$val['id']}}</td>
                                    <td>{{$val['name']}}</td>
                                    <td>{{$val['parent_name']}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default" type="button">Action</button>
                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"
                                                    type="button">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                <li><a href="{{route('editCountry', $val['id'])}}">Edit</a></li>
                                                @if($val['id'] != 1)
                                                    <li><a href="{{route('deleteCountry', $val['id'])}}">Delete</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{$countries->links()}}
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop