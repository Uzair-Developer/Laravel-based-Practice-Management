@extends('layout/main')

@section('title')
    - {{$ipToPrinter['ip'] ? 'Edit' : 'Add'}} IP To Printer
@stop

@section('header')

@stop

@section('footer')
    <script type="text/javascript">
        $(function () {

        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            {{$ipToPrinter['ip'] ? 'Edit' : 'Add'}} IP To Printer
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    {{Form::open()}}
                    <div class="box-body" id="tab_1">

                        <div class="form-group col-md-4">
                            <label>Hospital *</label>
                            <select autocomplete="off" required id="selectHospital2" name="hospital_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    @if(Input::old('hospital_id'))
                                        <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @else
                                        <option value="{{$val['id']}}" @if($ipToPrinter['hospital_id'] == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Printer IP *</label>
                            <input required autocomplete="off" type="text"
                                   name="ip" class="form-control"
                                   value="{{Input::old('ip') ? Input::old('ip') : $ipToPrinter['ip']}}">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Printer Name *</label>
                            <input required autocomplete="off" type="text"
                                   name="name" class="form-control"
                                   value="{{Input::old('name') ? Input::old('name') : $ipToPrinter['name']}}">
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('ipToPrinter')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop
