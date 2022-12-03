@extends('layout/main')

@section('title')
    - Edit System Root
@stop

@section('content')
    <section class="content-header">
        <h1>
            Edit System Root
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-9">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('route' => 'updateSystemRoot','role'=>"form",'files' => true))}}
                        <div class="box-body">
                            <div class="form-group">
                                <label for="exampleInputEmail1">System name</label>
                                <input type="text" name="system_name" value="{{$data['system_name']}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile">Logo</label>
                                <input type="file" name="logo">
                                @if(!empty($data['logo']))
                                    <img src="{{asset($data['logo'])}}" width="100" height="100">
                                @endif
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop