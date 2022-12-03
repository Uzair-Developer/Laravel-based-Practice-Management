@extends('layout/main')

@section('title')
    - Add Country
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script>
        $(function () {
            $(".select2").select2();
        })
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Add Country
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
                            <label>Parent</label>
                            <select autocomplete="off" class="form-control select2" name="parent_id" >
                                <option value="">Choose</option>
                                @foreach($parents as $val)
                                    @if(Input::old('parent_id')== $val['id'])
                                        <option selected="selected" value="{{$val['id']}}">{{$val['name']}}</option>
                                    @else
                                        <option value="{{$val['id']}}">{{$val['name']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Name</label>
                            <input required type="text" value="{{Input::old('name')}}" name="name" class="form-control">
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('listCountry')}}" class="btn btn-danger" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop


