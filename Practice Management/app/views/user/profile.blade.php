@extends('layout/main')

@section('title')
    - Profile {{$user['user_name']}}
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@stop

@section('content')
    <section class="content-header">
        <h1>
            Profile {{$user['user_name']}}
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('role'=>"form",'files' => true))}}
                    <div class="box-body">
                        <div class="form-group col-md-6">
                            <label>Full name</label>
                            <input autocomplete="off" required type="text" value="{{$user['full_name']}}" name="full_name"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input autocomplete="off" type="email" value="{{$user['email']}}" name="email" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Phone number</label>
                            <input autocomplete="off" type="number" value="{{$user['phone_number']}}"
                                   name="phone_number" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Mobile 1</label>
                            <input autocomplete="off" type="number" value="{{$user['mobile1']}}"
                                   name="mobile1" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Mobile 2</label>
                            <input autocomplete="off" type="number" value="{{$user['mobile2']}}"
                                   name="mobile2" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Address</label>
                            <input autocomplete="off" type="text" value="{{$user['address']}}"
                                   name="address" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Password</label>
                            <input autocomplete="off" type="password" name="password" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Confirmation Password</label>
                            <input autocomplete="off" type="password" name="password_confirmation" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Personal photo</label>
                            <input autocomplete="off" type="file" name="image_url">
                            @if(!empty($user['image_url']))
                                <img src="{{asset($user['image_url'])}}" width="100" height="100">
                            @endif
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

@section('footer')
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();
        });
    </script>
@stop
