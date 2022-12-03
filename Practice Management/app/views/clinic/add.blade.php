@extends('layout/main')

@section('title')
    - Add Clinic
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@stop

@section('content')
    <section class="content-header">
        <h1>
            Add Clinic
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-9">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('role'=>"form",'files' => true))}}
                    <div class="box-body">
                        <div class="form-group">
                            <label>Name En</label>
                            <input required type="text" value="{{Input::old('name')}}" name="name" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Name Ar</label>
                            <input type="text" value="{{Input::old('name_ar')}}" name="name_ar" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Code</label>
                            <input required type="text" value="{{Input::old('code')}}" name="code" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>SMS Code</label>
                            <input required type="text" value="{{Input::old('sms_code')}}" name="sms_code" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Hospital</label>
                            <select required name="hospital_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Category</label>
                            <select autocomplete="off" name="category_id"
                                    class="form-control select2" >
                                <option value="">Choose</option>
                                @foreach($category as $val)
                                    <option value="{{$val['id']}}"
                                            @if(Input::old('category_id') == $val['id'])
                                            selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('clinics')}}" class="btn btn-info" type="submit">Back</a>
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
            //Initialize Select2 Elements
            $(".select2").select2();
        });
    </script>
@stop