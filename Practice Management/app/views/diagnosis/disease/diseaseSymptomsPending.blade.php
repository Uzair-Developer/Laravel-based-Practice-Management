@extends('layout/main')

@section('title')
    - Disease Symptoms Relation
@stop

@section('content')
    <section class="content-header">
        <h1>
            Disease Symptoms Relation
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        Filters
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        {{Form::open(array('role'=>"form", 'method' => 'GET'))}}
                        <div class="form-group col-md-3">
                            <select class="form-control select2" name="status">
                                <option @if(Input::get('status') == '') selected @endif value="">Choose Status</option>
                                <option @if(Input::get('status') == '0') selected
                                        @endif value="0">Pending
                                </option>
                                <option @if(Input::get('status') == 1) selected
                                        @endif value="1">Approval
                                </option>
                                <option @if(Input::get('status') == 2) selected
                                        @endif value="2">Cancel
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <button class="btn btn-primary" type="submit">Search</button>
                            <a href="{{route('diseaseSymptomsPending')}}" class="btn btn-info">Clean</a>
                        </div>
                        {{Form::close()}}
                    </div>

                </div>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered" id="example1">
                            <thead>
                            <tr>
                                <th style="width: 15px">#</th>
                                <th>Disease name</th>
                                <th>Symptom name</th>
                                <th>Rate</th>
                                <th>Entry by</th>
                                <th>Status</th>
                                <th>Cancel Note</th>
                                <th>Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($diseaseSymptomsPending as $val)
                                <tr>
                                    <td>{{$val['id']}}</td>
                                    <td>{{$val['disease_name']}}</td>
                                    <td>{{$val['symptom_name']}}</td>
                                    <td>{{$val['rate']}}</td>
                                    <td>{{$val['user_name']}}</td>
                                    <td>
                                        @if($val['status'] == 1)
                                            Approval
                                        @elseif($val['status'] == 0)
                                            Pending
                                        @elseif($val['status'] == 2)
                                            Cancel
                                        @endif
                                    </td>
                                    <td>{{nl2br($val['cancel_note'])}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-default" type="button">Action</button>
                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"
                                                    type="button">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                                <li><a href="{{route('approveRelation', $val['id'])}}">Approval</a></li>
                                                <li>
                                                    <a style="cursor: pointer" symptomId="{{$val['symptom_id']}}"
                                                       rowId="{{$val['id']}}"
                                                       diseaseId="{{$val['disease_id']}}" rate="{{$val['rate']}}"
                                                       data-toggle="modal"
                                                       class="editRelation" data-target="#myModal">Edit</a>
                                                </li>
                                                <li><a href="{{route('cancelRelation', $val['id'])}}">Cancel</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{$links}}
                    </div>

                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Notes</h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'editRelation'))}}
                <div class="modal-body">

                    <div class="form-group">
                        <label>Symptoms</label>
                        <br>
                        <select style="width: 300px;" id="selectSymptoms" required name="symptom_id"
                                class="form-control select2">
                            <option value="">Choose</option>
                            @foreach($symptoms as $val)
                                <option value="{{$val['id']}}">{{$val['name']}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="id" id="rowId">
                    </div>

                    <div class="form-group">
                        <label>Diseases</label>
                        <br>
                        <select style="width: 300px;" id="selectDiseases" required name="disease_id"
                                class="form-control select2">
                            <option value="">Choose</option>
                            @foreach($diseases as $val)
                                <option value="{{$val['id']}}">{{$val['name']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Rate</label>
                        <input style="width: 300px;" id="rate" required name="rate" class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
@stop

@section('footer')
    <script>
        $(function () {
            $('.editRelation').click(function (e) {
                $("#rowId").val($(this).attr('rowId'));
                $("#selectSymptoms").val($(this).attr('symptomId')).select2();
                $("#selectDiseases").val($(this).attr('diseaseId')).select2();
                $("#rate").val($(this).attr('rate'));
            });
            $.fn.modal.Constructor.prototype.enforceFocus = function () {
            }; // very important for select2 in popup
        });

    </script>
@stop