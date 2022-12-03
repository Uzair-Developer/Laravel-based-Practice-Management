@extends('layout/main')

@section('title')
    - {{$kioskToPrinter['ip'] ? 'Edit' : 'Add'}} Kiosk To Printer
@stop

@section('header')

@stop

@section('footer')
    <script type="text/javascript">
        $(function () {

            $("#selectHospital2").change(function (e) {
                $("#printer_id").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPrinterByHospital')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#printer_id").removeAttr('disabled').html(data).select2();
                    }
                });
            });

            @if(Input::old('hospital_id') || $kioskToPrinter['hospital_id'])
            $("#printer_id").attr('disabled', 'disabled');
            var hospital_id = '{{Input::old('hospital_id') ? Input::old('hospital_id') : $kioskToPrinter['hospital_id']}}';
            $.ajax({
                url: '{{route('getPrinterByHospital')}}',
                method: 'POST',
                data: {
                    hospital_id: hospital_id
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#printer_id").removeAttr('disabled').html(data).select2();
                    @if(Input::old('printer_id') || $kioskToPrinter['printer_id'])
                    var printer_id = '{{Input::old('printer_id') ? Input::old('printer_id') : $kioskToPrinter['printer_id']}}';
                    $("#printer_id").val(printer_id).select2();
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
            {{$kioskToPrinter['ip'] ? 'Edit' : 'Add'}} Kiosk To Printer
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    {{Form::open()}}
                    <div class="box-body" id="tab_1">

                        <div class="form-group col-md-3">
                            <label>Hospital *</label>
                            <select autocomplete="off" required id="selectHospital2" name="hospital_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    @if(Input::old('hospital_id'))
                                        <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @else
                                        <option value="{{$val['id']}}" @if($kioskToPrinter['hospital_id'] == $val['id'])
                                        selected @endif>{{$val['name']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Kiosk IP *</label>
                            <input required autocomplete="off" type="text"
                                   name="ip" class="form-control"
                                   value="{{Input::old('ip') ? Input::old('ip') : $kioskToPrinter['ip']}}">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Kiosk Name *</label>
                            <input required autocomplete="off" type="text"
                                   name="name" class="form-control"
                                   value="{{Input::old('name') ? Input::old('name') : $kioskToPrinter['name']}}">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Printer *</label>
                            <select autocomplete="off" required name="printer_id" id="printer_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('kioskToPrinter')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop
