@extends('layout/main')

@section('title')
    - Pms Attributes
@stop

@section('content')
    <section class="content-header">
        <h1>
            Pms Attributes
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('role'=>"form"))}}
                    <div class="box-body">

                        <div class="form-group">
                            <label>Name</label>
                            <input required type="text"
                                   value="{{Input::old('name') ? Input::old('name') : $exceptionReason['name']}}"
                                   name="name" class="form-control">
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('listExceptionReason')}}" class="btn btn-danger" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop


