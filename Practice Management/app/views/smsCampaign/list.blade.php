@extends('layout/main')

@section('title')
    - SMS Campaign
@stop

@section('header')

@stop

@section('footer')
    <script src="{{asset('plugins/smsArea/smsarea.js')}}"></script>
    <script>
        $(function () {
            $('#smsText').smsArea({
                maxSmsNum:3,
                cut: true
            });
        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            SMS Campaign
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                {{Form::open(array('role'=>"form", 'route' => 'smsCampaignDownloadTemplate'))}}
                <button class="btn btn-primary" type="submit">Download Template</button>
                {{Form::close()}}
                <br>
            </div>

            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li id="tab-li_0" class="tab-li active"><a href="#tab_0" data-toggle="tab">New Group</a></li>
                        <li id="tab-li_1" class="tab-li"><a href="#tab_1" data-toggle="tab">Exist Group</a></li>
                    </ul>
                    <div class="tab-content col-md-12">
                        <div class="tab-pane active" id="tab_0">
                            {{Form::open(array('role'=>"form", 'files' => true, 'route' => 'smsCampaignSendNewGroup'))}}
                            <div class="form-group col-md-6">
                                <label>Group Name</label>
                                <input required type="text" name="group_name" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Campaign Name</label>
                                <input required type="text" name="campaign_name" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Import Template File</label>
                                <input required type="file" name="template" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Message</label>
                                <textarea id="smsText" required name="message" class="form-control"></textarea>
                                <b id="smsCount"></b> SMS (<b id="smsLength"></b>) Characters left
                            </div>
                            <div class="box-footer col-md-12">
                                <button class="btn btn-primary" type="submit">Send SMS</button>
                            </div>
                            {{Form::close()}}
                        </div>
                        <div class="tab-pane" id="tab_1">
                            {{Form::open(array('role'=>"form", 'route' => 'smsCampaignSendExistGroup'))}}
                            <div class="form-group col-md-6">
                                <label>Group Name</label>
                                <select required name="group_id" class="select2 form-control">
                                    <option value="">Choose</option>
                                    @foreach($smsGroup as $val)
                                        <option value="{{$val['id']}}">{{$val['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Campaign Name</label>
                                <input required type="text" name="campaign_name" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Message</label>
                                <textarea required name="message" class="form-control"></textarea>
                            </div>
                            <div class="box-footer col-md-12">
                                <button class="btn btn-primary" type="submit">Send SMS</button>
                            </div>
                            {{Form::close()}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@stop