@extends('layout/main')

@section('title')
    - Symptoms
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/autocomplete/jquery.autocomplete.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/autocomplete/jquery.autocomplete.js')}}"></script>
    <script>
        $("#QC_search").autocomplete({
            url: '{{route('autoCompleteSymptom')}}',
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
            List Symptoms
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <a href="{{route('addSymptom')}}">
                    <button class="btn btn-block btn-default">Add Symptom</button>
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
                    <div class="box-body">
                        {{Form::open(array('role'=>"form", 'method' => 'GET'))}}
                        <div class="form-group col-md-4">
                            <input id="QC_search" placeholder="Name" type="text" value="{{Input::get('symptom')}}"
                                   name="symptom"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <input placeholder="Organ Name" type="text" value="{{Input::get('organ')}}"
                                   name="organ"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <input name="search" class="btn btn-primary" type="submit" value="Search">
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
                                <th style="width: 70px">Ref ID</th>
                                <th>Name</th>
                                <th>Organ name</th>
                                <th>Options</th>
                            </tr>
                            @foreach($symptoms as $val)
                                <tr>
                                    <td>{{$val['id']}}</td>
                                    <td>{{$val['id_ref']}}</td>
                                    <td>{{$val['name']}}</td>
                                    <td>{{$val['organ_name']}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default" type="button">Action</button>
                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"
                                                    type="button">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                <li><a href="{{route('editSymptom', $val['id'])}}">Edit</a></li>
                                                <li><a href="{{route('deleteSymptom', $val['id'])}}">Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if(empty($symptoms))
                                <td colspan="7">
                                    <center>No Records!</center>
                                </td>
                            @endif
                            </tbody>
                        </table>
                        @if(Input::has('q'))
                            {{$symptoms->appends(['q' => Input::get('q')])->links()}}
                        @else
                            {{$symptoms->links()}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop