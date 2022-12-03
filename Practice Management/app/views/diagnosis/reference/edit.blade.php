@extends('layout/main')

@section('title')
    - Edit {{$reference['name']}}
@stop

@section('content')
    <section class="content-header">
        <h1>
            Edit {{$reference['name']}}
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-9">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('role'=>"form"))}}
                    <div class="box-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input required type="text" value="{{$reference['name']}}" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Link</label>
                            <input required type="url" value="{{$reference['link']}}" name="link" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control">{{$reference['notes']}}</textarea>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('listReference')}}" class="btn btn-danger" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop
