@extends('layout/main')

@section('title')
    - Edit {{$specialty['name']}}
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@stop

@section('content')
    <section class="content-header">
        <h1>
            Edit {{$specialty['name']}}
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
                            <label>Clinic</label>
                            <select required name="clinic_id" class="form-control select2">
                                @foreach($clinics as $val)
                                    <option value="{{$val['id']}}" @if($specialty['clinic_id'] == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Name</label>
                            <input required type="text" value="{{$specialty['name']}}" name="name" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea required name="text" class="form-control">{{$specialty['text']}}</textarea>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('listSpecialty')}}" class="btn btn-danger" type="submit">Back</a>
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
