@extends('layout/main')

@section('title')
    - Edit {{$clinic['name']}}
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@stop

@section('content')
    <section class="content-header">
        <h1>
            Edit {{$clinic['name']}}
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
                            <input required type="text" value="{{$clinic['name']}}" name="name" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Name Ar</label>
                            <input type="text" value="{{$clinic['name_ar']}}" name="name_ar" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Code</label>
                            <input required type="text" value="{{$clinic['code']}}" name="code" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>SMS Code</label>
                            <input required type="text" value="{{$clinic['sms_code']}}" name="sms_code" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputFile">Hospital</label>
                            <select required name="hospital_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    @if(Input::old('hospital_id'))
                                        <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @else
                                        <option value="{{$val['id']}}" @if($clinic['hospital_id'] == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @endif

                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Category</label>
                            <select autocomplete="off" name="category_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($category as $val)
                                    <option value="{{$val['id']}}"
                                            @if($clinic['category_id'] == $val['id'])
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