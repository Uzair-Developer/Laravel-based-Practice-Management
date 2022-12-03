@extends('layout/main')

@section('title')
    - Questions
@stop

@section('footer')
    <script>
        $(function () {

            $(".editQuestionBtn").click(function (e) {
                var ref_id = $(this).attr('ref_id');
                $.ajax({
                    url: '{{route('getQuestion')}}',
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
                            $("#edit-answer-type").val(data.answer_type).select2();
                            $("#editQuestion").modal('show');
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
            Questions
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
                            <label>Answer Type</label>
                            <select name="answer_type" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($answer_types as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('answer_type') == $val['id']) selected @endif>{{$val['title_en']}}</option>
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

            @if($c_user->user_type_id == 1 || $c_user->hasAccess('question.add'))
                <div class="col-md-3">
                    <a data-target="#addQuestion" data-toggle="modal">
                        <button class="btn btn-block btn-default">Add Question</button>
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
                                    <th>Answer Type</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($questions as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td>{{$val['title_ar']}}</td>
                                        <td>{{$val['title_en']}}</td>
                                        <td>{{$val['answer_type_name']}}</td>
                                        <td>
                                            <div class="btn-group" style="width: 150px;">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('question.edit'))
                                                    <a ref_id="{{$val['id']}}"
                                                       class="editQuestionBtn btn btn-default">Edit</a>

                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('question.delete'))
                                                    <a class="btn btn-danger ask-me"
                                                       href="javascript:void(0);" onclick="if(confirm('Are you sure?')) { window.location.replace('{{route('deleteQuestion', $val['id'])}}'); } return false"
                                                       style="cursor: pointer">Delete</a>

                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$questions->appends(Input::except('_token'))->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="addQuestion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Add Question</h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'createQuestion'))}}
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
                        <label>Answer Type * </label>
                        <br>
                        <select name="answer_type" class="form-control select2">
                            <option value="">Choose</option>
                            @foreach($answer_types as $val)
                                <option value="{{$val['id']}}">{{$val['title_en']}}</option>
                            @endforeach
                        </select>
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

    <div class="modal fade" id="editQuestion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Edit Question</h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'updateQuestion'))}}
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
                        <label>Answer Type * </label>
                        <br>
                        <select name="answer_type" id="edit-answer-type" class="form-control select2">
                            <option value="">Choose</option>
                            @foreach($answer_types as $val)
                                <option value="{{$val['id']}}">{{$val['title_en']}}</option>
                            @endforeach
                        </select>
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