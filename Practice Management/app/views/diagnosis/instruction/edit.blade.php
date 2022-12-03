@extends('layout/main')

@section('title')
    - Edit instructions
@stop

@section('content')
    <section class="content-header">
        <h1>
            Edit instructions
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
                            <label>New Patient Screen</label>
                            <textarea required
                                      name="new_patient" class="form-control">{{$instruction['new_patient']}}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Choose Symptoms Screen</label>
                            <textarea required
                                      name="choose_symptoms" class="form-control">{{$instruction['choose_symptoms']}}</textarea>
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
