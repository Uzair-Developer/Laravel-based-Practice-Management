@extends('layout/main')

@section('title')
    - Add Organ
@stop

@section('content')
    <section class="content-header">
        <h1>
            Add Organ
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
                            <input required type="text" value="{{Input::old('name')}}" name="name" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Ref Id</label>
                            <input required type="text" value="{{Input::old('ref_id')}}" name="ref_id" class="form-control">
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('listOrgan')}}" class="btn btn-danger" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop
