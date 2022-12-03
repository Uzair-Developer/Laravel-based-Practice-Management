@extends('layout/main')

@section('title')
    - Add Disease
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@stop

@section('content')
    <section class="content-header">
        <h1>
            Add Disease
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-9">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('role'=>"form"))}}
                    <div class="box-body">

                        <div class="form-group col-md-6">
                            <label>Specialty</label>
                            <select required name="specialty_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($specialties as $val)
                                    <option value="{{$val['id']}}" @if(Input::old('specialty_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Name</label>
                            <input required type="text" value="{{Input::old('name')}}" name="name" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Type</label>
                            <select required name="type" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach(\core\diagnosis\enums\DiseaseAttr::$type as $key => $val)
                                    <option value="{{$key}}" @if(Input::old('type') == $key)
                                    selected @endif>{{$val}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Age from</label>
                            <input required type="number" min="1" max="150" value="{{Input::old('age_from')}}"
                                   name="age_from" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Age to</label>
                            <input required type="number" min="1" max="150" value="{{Input::old('age_to')}}"
                                   name="age_to" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Gender</label>

                            <div class="radio">
                                <label>
                                    <input @if(Input::old('gender') == 1) checked @endif type="radio" value="1"
                                           name="gender">
                                    Male
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input @if(Input::old('gender') == 2) checked @endif type="radio" value="2"
                                           name="gender">
                                    Female
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input @if(Input::old('gender') == 3) checked @endif type="radio" value="3"
                                           name="gender">
                                    Both
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Ref id</label>
                            <input type="text" value="{{Input::old('id_ref')}}"
                                   name="id_ref" class="form-control">
                        </div>

                        <div class="form-group col-md-12">
                            <label>Description</label>
                            <textarea name="text" class="form-control">{{Input::old('text')}}</textarea>
                        </div>

                        <div class="box col-md-12">
                            <div class="box-header">
                                Symptoms
                                <button type="button" class="btn btn-default" style="margin-left: 5%;"
                                        data-toggle="modal" data-target="#myModal">
                                    Add symptom
                                </button>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <table id="tableSymptoms" class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <th style="width: 400px;">Symptom</th>
                                        <th style="width: 100px;">Rate (%)</th>
                                        <th style="width: 100px">Actions</th>
                                    </tr>
                                    <tr id="tr_symptom_0">
                                        <td>
                                            <select required name="symptom_id[]" style="width: 400px;"
                                                    class="form-control select2 symptoms">
                                                <option selected value="">Choose</option>
                                                @foreach($symptoms as $val)
                                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input required type="text" name="symptomRate[]" class="form-control">
                                        </td>
                                        <td><a class="btn btn-primary deleteSymptomRow"
                                               trDelete="tr_symptom_0">Delete</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="box-footer">
                                <a id="addSymptomRow" class="btn btn-primary">New row</a>
                            </div>
                        </div>

                        <div class="box col-md-12">
                            <div class="box-header">
                                Questions
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <table id="tableQuestions" class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <th>Question</th>
                                        <th style="width: 150px">Score (5 - 1)</th>
                                        <th style="width: 100px">Actions</th>
                                    </tr>
                                    <tr id="tr_question_0">
                                        <td>
                                            <input required type="text" name="questionText[]" class="form-control">
                                        </td>
                                        <td><input required type="number" min="1" max="5" name="questionScore[]"
                                                   class="form-control">
                                        </td>
                                        <td><a class="btn btn-primary deleteQuestionRow"
                                               trDelete="tr_question_0">Delete</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="box-footer">
                                <a id="addQuestionRow" class="btn btn-primary">New row</a>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('listDisease')}}" class="btn btn-danger" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Symptom</h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'createSymptomInDisease'))}}
                <div class="modal-body">
                    <div class="form-group">
                        <label>Organs</label>
                        <br>
                        <select style="width: 400px;" required name="organ_id" class="form-control select2">
                            @foreach($organs as $val)
                                <option value="{{$val['id']}}" @if(Input::old('organ_id') == $val['id'])
                                selected @endif>{{$val['name']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Name</label>
                        <input style="width: 400px;" required type="text" value="{{Input::old('name')}}"
                               name="name" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Type</label>
                        <br>
                        <select required name="type" class="form-control select2" style="width: 400px;">
                            <option selected value="">Choose</option>
                            @foreach(\core\diagnosis\enums\SymptomAttr::$type as $key => $val)
                                <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>S/S</label>
                        <br>
                        <select required name="s_s" class="form-control select2" style="width: 400px;">
                            <option selected value="">Choose</option>
                            @foreach(\core\diagnosis\enums\SymptomAttr::$SS as $key => $val)
                                <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea style="width: 400px;" name="text"
                                  class="form-control">{{Input::old('text')}}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
@stop

@section('footer')
    <script src="{{asset('plugins/underscore/underscore.js')}}"></script>
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();

            var trNum = 1;
            $("#addSymptomRow").click(function (e) {
                e.preventDefault();
                var template = _.template($('#symptomsScript').html(), {
                    trNum: trNum
                });
                $('#tableSymptoms tbody').append(template);
                $(".select22").select2();
                $('.select22').addClass('select2').removeClass('select22');
                ++trNum;
            });

            $(document).on('click', '.deleteSymptomRow', function (e) {
                var tr = $(this).attr('trDelete');
                $("#" + tr).remove();
            });

            var trNum2 = 1;
            $("#addQuestionRow").click(function (e) {
                e.preventDefault();
                var template = _.template($('#questionsScript').html(), {
                    trNum: trNum2
                });
                $('#tableQuestions tbody').append(template);
                ++trNum2;
            });

            $(document).on('change', '.symptoms', function (e) {
                var currentSymptom = $(this).val();
                var duplicate = 0;
                $(".symptoms").each(function (index) {
                    if ($(this).val() == currentSymptom) {
                        ++duplicate;
                    }
                });
                if (duplicate > 1) {
                    alert('This symptom is already joint');
                    $(this).val('');
                    $(this).select2();
                }
            });

            $(document).on('click', '.deleteQuestionRow', function (e) {
                var tr = $(this).attr('trDelete');
                $("#" + tr).remove();
            })
        });
    </script>
    <script id="symptomsScript" type="text/html">
        <tr id="tr_symptom_<%= trNum %>">
            <td>
                <select required name="symptom_id[]" style="width: 400px;" class="form-control symptoms select22">
                    <option selected value="">Choose</option>
                    @foreach($symptoms as $val)
                        <option value="{{$val['id']}}">{{$val['name']}}</option>
                    @endforeach
                </select>
            </td>
            <td><input required type="text" name="symptomRate[]" class="form-control"></td>
            <td><a class="btn btn-primary deleteSymptomRow" trDelete="tr_symptom_<%= trNum %>">Delete</a></td>
        </tr>
    </script>

    <script id="questionsScript" type="text/html">
        <tr id="tr_question_<%= trNum %>">
            <td>
                <input required type="text" name="questionText[]" class="form-control">
            </td>
            <td><input required type="number" min="1" max="5" name="questionScore[]" class="form-control">
            </td>
            <td><a class="btn btn-primary deleteSymptomRow" trDelete="tr_question_<%= trNum %>">Delete</a></td>
        </tr>
    </script>
@stop
