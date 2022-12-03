@extends('layout/main')

@section('title')
    - Diseases
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/autocomplete/jquery.autocomplete.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/autocomplete/jquery.autocomplete.js')}}"></script>
    <script>
        $("#QC_search").autocomplete({
            url: '{{route('autoCompleteDisease')}}',
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
            List Diseases
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <a href="{{route('addDisease')}}">
                    <button class="btn btn-block btn-default">Add Disease</button>
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
                        <div class="form-group col-md-6">
                            <input id="QC_search" placeholder="Name or Ref id" type="text" value="{{Input::get('q')}}" name="q" class="form-control">
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
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <th style="width: 15px">#</th>
                                <th style="width: 80px">Ref ID</th>
                                <th>Name</th>
                                <th>Specialty name</th>
                                <th>Options</th>
                            </tr>
                            @foreach($diseases as $val)
                                <tr>
                                    <td>{{$val['id']}}</td>
                                    <td>{{$val['id_ref']}}</td>
                                    <td>{{$val['name']}}</td>
                                    <td>{{$val['specialty_name']}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default" type="button">Action</button>
                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"
                                                    type="button">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                <li><a href="{{route('editDisease', $val['id'])}}">Edit</a></li>
                                                <li><a href="{{route('deleteDisease', $val['id'])}}">Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if(empty($diseases))
                                <td colspan="7">
                                    <center>No Records!</center>
                                </td>
                            @endif
                            </tbody>
                        </table>
                        @if(Input::has('q'))
                            {{$diseases->appends(['q' => Input::get('q')])->links()}}
                        @else
                            {{$diseases->links()}}
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>
@stop