@extends('layout/main')

@section('title')
    - Answer Types
@stop

@section('footer')
    <script>
        $(function () {

            $(".editAnswerTypeBtn").click(function (e) {
                var ref_id = $(this).attr('ref_id');
                $.ajax({
                    url: '{{route('getAnswerType')}}',
                    method: 'POST',
                    data: {
                        id: ref_id
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        if (data) {
                            $("#edit_id").val(data.id);
                            $("#edit_title_ar").val(data.title_ar);
                            $("#edit_title_en").val(data.title_en);
                            $("#edit_type").val(data.type).select2();
                            $("#edit-answers-ar").val(data.answers_ar);
                            $("#edit-answers-en").val(data.answers_en);
                            $("#editAnswerType").modal('show');
                        }
                    }
                });
            });
        })
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1>
            Answer Types
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        Search
                        <button type="button" class="btn btn-box-tool pull-right"
                                data-widget="collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                    <!-- /.box-header -->
                    {{Form::open(array('role'=>"form",'method' => 'GET'))}}
                    <div class="box-body">

                        <div class="form-group col-md-3">
                            <label>Type</label>
                            <select name="type" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($types as $key => $val)
                                    <option value="{{$key}}" @if(Input::get('type') == $key)
                                    selected @endif>{{$val}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>&nbsp;</label>
                            <br>
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </div>
                    {{Form::close()}}
                </div>
            </div>

            @if($c_user->user_type_id == 1 || $c_user->hasAccess('answerType.add'))
                <div class="col-md-3">
                    <a data-target="#addAnswerType" data-toggle="modal">
                        <button class="btn btn-block btn-default">Add Answer Type</button>
                    </a>
                    <br>
                </div>
            @endif
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th style="width: 15px">#</th>
                                    <th>Title (AR)</th>
                                    <th>Title (EN)</th>
                                    <th>Type</th>
                                    <th>Answers (AR)</th>
                                    <th>Answers (EN)</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($answerTypes as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td>{{$val['title_ar']}}</td>
                                        <td>{{$val['title_en']}}</td>
                                        <td>{{($val['type']==1?"Single selection":"Multi selection")}}</td>
                                        <td>{{$val['answers_ar']}}</td>
                                        <td>{{$val['answers_en']}}</td>
                                        <td>
                                            <div class="btn-group" style="width: 150px;">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('answerType.edit'))
                                                    <a ref_id="{{$val['id']}}"
                                                       class="editAnswerTypeBtn btn btn-default">Edit</a>

                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('answerType.delete'))
                                                    <a class="btn btn-danger ask-me"
                                                       href="javascript:void(0);" onclick="if(confirm('Are you sure?')) { window.location.replace('{{route('deleteAnswerType', $val['id'])}}'); } return false"
                                                       style="cursor: pointer">Delete</a>

                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$answerTypes->appends(Input::except('_token'))->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="addAnswerType" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Add Answer Type</h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'createAnswerType'))}}
                <div class="modal-body col-md-12">

                    <div class="form-group col-md-12">
                        <label>Title (ar) * </label>
                        <br>
                        <input required name="title_ar" class="form-control" type="text"/>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Title (en) * </label>
                        <br>
                        <input required name="title_en" class="form-control" type="text"/>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Type * </label>
                        <br>
                        <select autocomplete="off" id="type" required name="type" class="form-control select2">
                            <option value="">Choose</option>
                            @foreach($types as $key => $val)
                                <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Answers (AR) * </label>
                        <br>
                        <input required name="answers_ar" class="form-control" type="text"/>
                        <div class="help-block">answers separated by comma(,)</div>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Answers (EN) * </label>
                        <br>
                        <input required name="answers_en" class="form-control" type="text"/>
                        <div class="help-block">answers separated by comma(,)</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAnswerType" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Edit Answer Type</h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'updateAnswerType'))}}
                <div class="modal-body col-md-12">
                    <input type="hidden" name="id" id="edit_id" />
                    <div class="form-group col-md-12">
                        <label>Title (ar) * </label>
                        <br>
                        <input required name="title_ar" id="edit_title_ar" class="form-control" type="text"/>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Title (en) * </label>
                        <br>
                        <input required name="title_en" id="edit_title_en" class="form-control" type="text"/>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Type * </label>
                        <br>
                        <select autocomplete="off" id="edit_type" required name="type" class="form-control select2">
                            <option value="">Choose</option>
                            @foreach($types as $key => $val)
                                <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Answers (AR) * </label>
                        <br>
                        <input required name="answers_ar" class="form-control" id="edit-answers-ar" type="text"/>
                        <div class="help-block">answers separated by comma(,)</div>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Answers (EN) * </label>
                        <br>
                        <input required name="answers_en" class="form-control" id="edit-answers-en" type="text"/>
                        <div class="help-block">answers separated by comma(,)</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
@stop