@extends('layout/main')

@section('title')
    - {{ !empty($survey['id'])?'Edit':'Add' }} Survey
@stop

@section('footer')
    <script>
        $(function () {
            $("#new_ex_group").on("click", function (e) {
                e.preventDefault();
                $("#ex-group-block").find("select.group_id").select2('destroy');
                $("#ex-group-block").find("select.question_id").select2('destroy');
                var clone = $("#ex-group-block").clone(false);
                clone.find("select").val("");
                clone.attr("id", "");
                clone.append('<a href="#" style="float: right;margin-right: -9px;margin-top: -42px;" onclick="$(this).parent().remove(); return false;"><i class="fa fa-trash" style="color:red"></i></a>');
                clone.appendTo("#ex-group-wrapper");
                $("#ex-group-block").find("select.group_id").select2();
                $("#ex-group-block").find("select.question_id").select2();
                clone.find("select.group_id").select2();
                clone.find("select.question_id").select2();
            });
            $("#addSurveyGroup").submit(function(e){
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
                        $("#addGroup").modal("hide");
                        //location.replace('{{ Request::url() }}');
                        refetch_groups();
                    }

                });
                return false;
            });
            function refetch_groups() {
                $.ajax({
                    url: "{{ route('getAllGroupsHtml') }}",
                    method: "GET",
                    headers: {token: '{{csrf_token()}}'},
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        var selectedItems=[];
                        $(".group_id").each(function(i, t){
                            selectedItems.push($(this).val());
                            $(this).select2('destroy');
                            $(this).html(res);
                            $(this).select2();
                        });
                        $(".group_id").each(function(i, t){
                            $(this).select2('destroy');
                            $(this).val(selectedItems[i]);
                            $(this).select2();
                        });
                    }

                });
            }
        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1>
            {{ !empty($survey['id'])?'Edit':'Add' }} Survey
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('role'=>"form"))}}
                    <div class="box-body">
                        <h4>Survey details</h4><br/>
                        <div class="form-group col-md-6">
                            <label>Header En *</label>
                            <input required type="text" value="{{Input::old('header_en')?Input::old('header_en'):$survey['header_en']}}" name="header_en"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Header Ar *</label>
                            <input required type="text" value="{{Input::old('header_ar')?Input::old('header_ar'):$survey['header_ar']}}" name="header_ar"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Footer En</label>
                            <input type="text" value="{{Input::old('footer_en')?Input::old('footer_en'):$survey['footer_en']}}" name="footer_en"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Footer Ar</label>
                            <input type="text" value="{{Input::old('footer_ar')?Input::old('footer_ar'):$survey['footer_ar']}}" name="footer_ar" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Description En</label>
                            <textarea name="description_en"
                                      class="form-control">{{Input::old('description_en')?Input::old('description_en'):$survey['description_en']}}</textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Description Ar</label>
                            <textarea name="description_ar"
                                      class="form-control">{{Input::old('description_ar')?Input::old('description_ar'):$survey['description_ar']}}</textarea>
                        </div>
                    </div>
                    <div class="box-body">
                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('surveyGroup.add'))
                            <a data-toggle="modal" data-target="#addGroup" class="btn btn-primary pull-right">Add Survey Group</a>
                        @endif
                        <h4>Assign to groups and questions</h4><br/>
                        <div class="row">
                            <div class="col-md-10" id="ex-group-wrapper">
                                @if(isset($selectedGroupsAndQuestions) and count($selectedGroupsAndQuestions) and isset($selectedGroupsAndQuestions[0]['group_id']))
                                    @foreach($selectedGroupsAndQuestions as $k => $sel)
                                        <div id="{{ $k==0?"ex-group-block":"" }}" class="ex-group-block">
                                            <div class="form-group col-md-6">
                                                <label>Group</label>
                                                <select name="ex_group[group_id][]" class="form-control group_id select2">
                                                    <option value="">Choose</option>
                                                    @foreach($groups as $val)
                                                        <option value="{{$val['id']}}" {{$val['id']==$sel['group_id']?"selected":""}}>{{$val['title_en']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Question</label>
                                                <select name="ex_group[question_id][]" class="form-control question_id select2"
                                                        >
                                                    <option value="">Choose</option>
                                                    @foreach($questions as $val)
                                                        <option value="{{$val['id']}}" {{$val['id']==$sel['question_id']?"selected":""}}>{{$val['title_en']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            {{ $k!=0?'<a href="#" style="float: right;margin-right: -9px;margin-top: -42px;" onclick="$(this).parent().remove(); return false;"><i class="fa fa-trash" style="color:red"></i></a>':'' }}
                                        </div>
                                    @endforeach
                                @else
                                    <div id="ex-group-block" class="ex-group-block">
                                        <div class="form-group col-md-6">
                                            <label>Group</label>
                                            <select name="ex_group[group_id][]" class="form-control group_id select2">
                                                <option value="">Choose</option>
                                                @foreach($groups as $val)
                                                    <option value="{{$val['id']}}">{{$val['title_en']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Question</label>
                                            <select name="ex_group[question_id][]" class="form-control question_id select2"
                                                    >
                                                <option value="">Choose</option>
                                                @foreach($questions as $val)
                                                    <option value="{{$val['id']}}">{{$val['title_en']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-2" style="margin-top: 29px;">
                                <a href="#" id="new_ex_group"><i class="fa fa-plus-circle"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('listSurvey')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="addGroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Add Survey Group</h4>
                </div>
                {{Form::open(array('role'=>"form", 'id' => 'addSurveyGroup'))}}
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

@stop