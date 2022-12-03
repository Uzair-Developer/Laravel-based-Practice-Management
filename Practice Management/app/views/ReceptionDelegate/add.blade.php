@extends('layout/main')

@section('title')
    - {{$receptionDelegate['id'] ? 'Edit' : 'Add'}} Reception Delegate
@stop

@section('header')

@stop

@section('footer')
    <script type="text/javascript">
        $(function () {

            $("#selectHospital2").change(function (e) {
                $("#reception_id, #reception1_delegate_id, #reception2_delegate_id, #reception3_delegate_id").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getRoomsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#reception_id, #reception1_delegate_id, #reception2_delegate_id, #reception3_delegate_id").removeAttr('disabled').html(data.screens).select2();
                    }
                });
            });

                    @if(Input::old('hospital_id') || $receptionDelegate['hospital_id'])
                                var hospital_id = '{{Input::old('hospital_id') ? Input::old('hospital_id') : $receptionDelegate['hospital_id']}}';
            $.ajax({
                url: '{{route('getRoomsByHospitalId')}}',
                method: 'POST',
                data: {
                    hospital_id: hospital_id
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#reception_id, #reception1_delegate_id, #reception2_delegate_id, #reception3_delegate_id").removeAttr('disabled').html(data.screens).select2();
                    @if(Input::old('reception_id') || $receptionDelegate['reception_id'])
                    var reception_id = '{{Input::old('reception_id') ? Input::old('reception_id') : $receptionDelegate['reception_id']}}';
                    $("#reception_id").val(reception_id).select2();
                    @endif

                            @if(Input::old('reception1_delegate_id') || $receptionDelegate['reception1_delegate_id'])
                    var reception1_delegate_id = '{{Input::old('reception1_delegate_id') ? Input::old('reception1_delegate_id') : $receptionDelegate['reception1_delegate_id']}}';
                    $("#reception1_delegate_id").val(reception1_delegate_id).select2();
                    @endif

                            @if(Input::old('reception2_delegate_id') || $receptionDelegate['reception2_delegate_id'])
                    var reception2_delegate_id = '{{Input::old('reception2_delegate_id') ? Input::old('reception2_delegate_id') : $receptionDelegate['reception2_delegate_id']}}';
                    $("#reception2_delegate_id").val(reception2_delegate_id).select2();
                    @endif

                            @if(Input::old('reception3_delegate_id') || $receptionDelegate['reception3_delegate_id'])
                    var reception3_delegate_id = '{{Input::old('reception3_delegate_id') ? Input::old('reception3_delegate_id') : $receptionDelegate['reception3_delegate_id']}}';
                    $("#reception3_delegate_id").val(reception3_delegate_id).select2();
                    @endif
                }
            });
            @endif

        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            {{$receptionDelegate['id'] ? 'Edit' : 'Add'}} Reception Delegate
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    {{Form::open()}}
                    <div class="box-body" id="tab_1">

                        <div class="form-group col-md-6">
                            <label>Hospital *</label>
                            <select autocomplete="off" required id="selectHospital2" name="hospital_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    @if(Input::old('hospital_id'))
                                        <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @else
                                        <option value="{{$val['id']}}" @if($receptionDelegate['hospital_id'] == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Reception</label>
                            <select autocomplete="off" id="reception_id" name="reception_id" class="form-control select2">
                                <option value="">Choose</option>

                            </select>
                        </div>

                        <div class="clearfix">
                        </div>

                        <div class="form-group col-md-4">
                            <label>Rec. Delegate 1</label>
                            <select autocomplete="off" id="reception1_delegate_id" name="reception1_delegate_id" class="form-control select2">
                                <option value="">Choose</option>

                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Rec. Delegate 2</label>
                            <select autocomplete="off" id="reception2_delegate_id" name="reception2_delegate_id" class="form-control select2">
                                <option value="">Choose</option>

                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Rec. Delegate 3</label>
                            <select autocomplete="off" id="reception3_delegate_id" name="reception3_delegate_id" class="form-control select2">
                                <option value="">Choose</option>

                            </select>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('receptionDelegate')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop
