@extends('layout/main')

@section('title')
    - Survey Groups
@stop

@section('footer')
    <script>
        $(function(){
            $("#surveyGroupForm").submit(function(e){
                e.preventDefault();
                var form = new FormData(this);
                $.ajax({
                    url: "{{ route('createSurveyGroup') }}",
                    data: form,
                    method: "POST",
                    headers: {token: '{{csrf_token()}}'},
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function (res) {
                        alert(res.msg);
                        $("#addSurveyGroup").modal("hide");
                        location.replace('{{ Request::url() }}');
                    }

                });
                return false;
            });

            $(".editSurveyGroupBtn").click(function (e) {
                var ref_id = $(this).attr('ref_id');
                $.ajax({
                    url: '{{route('getSurveyGroup')}}',
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
                            $("#editSurveyGroup").modal('show');
                        }
                    }
                });
            });

            $("#updateSurveyGroupForm").submit(function(e){
                e.preventDefault();
                var form = new FormData(this);
                $.ajax({
                    url: "{{ route('updateSurveyGroup') }}",
                    data: form,
                    method: "POST",
                    headers: {token: '{{csrf_token()}}'},
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function (res) {
                        alert(res.msg);
                        $("#editSurveyGroup").modal("hide");
                        location.replace('{{ Request::url() }}');
                    }

                });
                return false;
            });
        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1>
            Survey Groups
        </h1>
    </section>

    <section class="content">
        <div class="row">

            @if($c_user->user_type_id == 1 || $c_user->hasAccess('surveyGroup.add'))
                <div class="col-md-3">
                    <a data-target="#addSurveyGroup" data-toggle="modal">
                        <button class="btn btn-block btn-default">Add Survey Group</button>
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
                                    <th>Title(AR)</th>
                                    <th>Title(EN)</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($surveyGroups as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td>{{$val['title_ar']}}</td>
                                        <td>{{$val['title_en']}}</td>
                                        <td>
                                            <div class="btn-group" style="width: 150px;">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('surveyGroup.edit'))
                                                    <a ref_id="{{$val['id']}}" class="editSurveyGroupBtn btn btn-default">Edit</a>

                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('surveyGroup.delete'))
                                                    <a class="btn btn-danger ask-me"
                                                       href="javascript:void(0);" onclick="if(confirm('Are you sure?')) { window.location.replace('{{route('deleteSurveyGroup', $val['id'])}}'); } return false"
                                                       style="cursor: pointer">Delete</a>

                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$surveyGroups->appends(Input::except('_token'))->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="addSurveyGroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Add Survey Group</h4>
                </div>
                {{Form::open(array('role'=>"form", 'id' => 'surveyGroupForm'))}}
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

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSurveyGroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Edit Survey Group</h4>
                </div>
                {{Form::open(array('role'=>"form", 'id' => 'updateSurveyGroupForm'))}}
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