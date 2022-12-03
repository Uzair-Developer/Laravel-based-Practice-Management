@extends('layout/main')

@section('header')

@stop

@section('title')
    - Website Settings
@stop

@section('footer')
    <script src="https://cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('ckeditor');
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Website Settings
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                {{Form::open()}}
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li id="tab-li_0" class="tab-li active">
                                    <a href="#tab_0" data-toggle="tab">English</a></li>
                                <li id="tab-li_1" class="tab-li">
                                    <a href="#tab_1" data-toggle="tab">Arabic</a></li>
                            </ul>
                            <div class="tab-content col-md-12" id="loading">
                                <div class="tab-pane active" id="tab_0">
                                    <div class="form-group col-md-12">
                                        <label>Patient Instruction *</label>
                                        <textarea required name="patient_instruction_en"
                                                  class="form-control ckeditor">{{$settings['patient_instruction_en']}}</textarea>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_1">
                                    <div class="form-group col-md-12">
                                        <label>Patient Instruction *</label>
                                        <textarea required name="patient_instruction_ar"
                                                  class="form-control ckeditor">{{$settings['patient_instruction_ar']}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </section>
@stop
