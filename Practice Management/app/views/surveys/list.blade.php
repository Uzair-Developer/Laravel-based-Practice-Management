@extends('layout/main')

@section('title')
    - Surveys
@stop

@section('footer')
    <script>

    </script>
@stop

@section('content')

    <section class="content-header">
        <h1>
            Surveys
        </h1>
    </section>

    <section class="content">
        <div class="row">

            @if($c_user->user_type_id == 1 || $c_user->hasAccess('survey.add'))
                <div class="col-md-3">
                    <a href="{{ route('addSurvey') }}">
                        <button class="btn btn-block btn-default">Add Survey</button>
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
                                    <th>Header (AR)</th>
                                    <th>Header (EN)</th>
                                    <th>Footer (AR)</th>
                                    <th>Footer (EN)</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($surveys as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td>{{$val['header_ar']}}</td>
                                        <td>{{$val['header_en']}}</td>
                                        <td>{{$val['footer_ar']}}</td>
                                        <td>{{$val['footer_en']}}</td>
                                        <td>
                                            <div class="btn-group" style="width: 150px;">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('survey.edit'))
                                                    <a href="{{ route('editSurvey', $val['id']) }}"
                                                       class="btn btn-default">Edit</a>

                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('survey.delete'))
                                                    <a class="btn btn-danger ask-me"
                                                       href="javascript:void(0);" onclick="if(confirm('Are you sure?')) { window.location.replace('{{route('deleteSurvey', $val['id'])}}'); } return false"
                                                       style="cursor: pointer">Delete</a>

                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$surveys->appends(Input::except('_token'))->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop