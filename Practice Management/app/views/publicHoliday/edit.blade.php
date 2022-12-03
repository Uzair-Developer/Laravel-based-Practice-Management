@extends('layout/main')

@section('title')
    - Edit {{$data['name']}}
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
@stop

@section('content')
    <section class="content-header">
        <h1>
            Edit {{$data['name']}}
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('role'=>"form"))}}
                    <div class="box-body">
                        <div class="form-group col-md-6">
                            <label>Hospital</label>
                            <select required name="hospital_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    @if(Input::old('hospital_id'))
                                        <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @else
                                        <option value="{{$val['id']}}" @if($data['hospital_id'] == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">Name</label>
                            <input required type="text" value="{{$data['name']}}" name="name" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>From Date</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{$data['from_date']}}"
                                   name="from_date" class="form-control datepicker">
                        </div>

                        <div class="form-group col-md-6">
                            <label>To Date</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{$data['to_date']}}"
                                   name="to_date" class="form-control datepicker">
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('publicHoliday')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

    <script type="text/javascript">
        $(function () {
            $('.datepicker').datepicker({
                todayHighlight: true,
                autoclose: true
            });
            $(".select2").select2();
        });
    </script>
@stop