@extends('layout/main')

@section('title')
    - Edit {{$symptom['name']}}
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@stop

@section('content')
    <section class="content-header">
        <h1>
            Edit {{$symptom['name']}}
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
                            <label>Organs</label>
                            <select required name="organ_id" class="form-control select2">
                                @foreach($organs as $val)
                                    <option value="{{$val['id']}}" @if($symptom['organ_id'] == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Name</label>
                            <input required type="text" value="{{$symptom['name']}}" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select required name="type" class="form-control select2">
                                <option selected value="">Choose</option>
                                @foreach(\core\diagnosis\enums\SymptomAttr::$type as $key => $val)
                                    <option @if($symptom['type'] == $key)
                                            selected @endif value="{{$key}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>S/S</label>
                            <select required name="s_s" class="form-control select2" >
                                <option selected value="">Choose</option>
                                @foreach(\core\diagnosis\enums\SymptomAttr::$SS as $key => $val)
                                    <option @if($symptom['s_s'] == $key)
                                            selected @endif value="{{$key}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="text" class="form-control">{{$symptom['text']}}</textarea>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('listSymptom')}}" class="btn btn-danger" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop

@section('footer')
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script>
        $(function () {
            $(".select2").select2();
        });
    </script>
@stop
