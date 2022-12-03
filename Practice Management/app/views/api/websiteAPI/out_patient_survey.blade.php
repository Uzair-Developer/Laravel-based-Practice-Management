<style>
    .surveyDivs {
        display: none;
    }
</style>
<div class="col-md-12">
    <div class="box box-primary">
        <!-- /.box-header -->
        <div class="box-body">
            <form id="survey_form">
                <div class="form-group col-md-4 @if($lang == 'en') pull-right @endif">
                    <img src="{{asset('images/sgh-logo5.png')}}">
                </div>
                <div class="form-group col-md-8">
                    <p>
                        @if($lang == 'ar')
                            {{nl2br($survey['description_ar'])}}
                        @else
                            {{nl2br($survey['description_en'])}}
                        @endif
                    </p>
                </div>

                <div class="form-group col-md-5 col-xs-12 @if($lang == 'ar') pull-right @endif">
                    @if($lang == 'ar')
                        <label>فضلا ادخل رقم الكارت الطبى الخاص بك بالمستشفى PIN Number</label>
                    @else
                        <label>Please enter your PIN (The number in your card of the hospital)</label>
                    @endif
                    <input id="patient_id" class="form-control" autocomplete="off" name="id" type="text"/>
                </div>
                <div class="form-group col-md-6 col-xs-12 @if($lang == 'ar') pull-right @endif">
                    <label>&nbsp;</label>

                    <div>
                        <button id="patient_id_btn" class="btn btn-info" type="button">
                            @if($lang == 'ar')
                                إبدأ
                            @else
                                Start
                            @endif
                        </button>
                    </div>
                </div>

                <div class="form-group col-md-4 col-xs-12 surveyDivs @if($lang == 'ar') pull-right @endif">
                    <label>
                        @if($lang == 'ar')
                            إسم المريض:
                        @else
                            Patient Name:
                        @endif
                    </label>
                    {{ucwords(strtolower($reservation['patient_name']))}}

                </div>
                <div class="form-group col-md-4 surveyDivs @if($lang == 'ar') pull-right @endif">
                    <label>
                        @if($lang == 'ar')
                            إسم العياده:
                        @else
                            Clinic Name:
                        @endif
                    </label>
                    {{ucwords(strtolower($reservation['clinic_name']))}}

                </div>
                <div class="form-group col-md-4 col-xs-12 surveyDivs @if($lang == 'ar') pull-right @endif">
                    <label>
                        @if($lang == 'ar')
                            إسم الدكتور:
                        @else
                            Doctor Name:
                        @endif
                    </label>
                    {{ucwords(strtolower($reservation['physician_name']))}}

                </div>
                <div class="form-group col-md-12 col-xs-12 surveyDivs">
                    <strong>
                        @if($lang == 'ar')
                            فضلا ضع تقييمك للتالى:
                        @else
                            Please rate the following:
                        @endif
                    </strong>
                </div>

                @foreach($surveyToGroups as $group)
                    <div class="form-group col-md-12 col-xs-12 surveyDivs">
                        <strong style="color: black;font-size: 20px;">
                            @if($lang == 'ar')
                                <label>{{$group['group']['title_ar']}}</label>
                            @else
                                <label>{{$group['group']['title_en']}}</label>
                            @endif
                        </strong>
                        @foreach($group['group']['questions'] as $question)
                            <div>
                                <label style="font-weight: bold;">
                                    @if($lang == 'ar')
                                        {{$question['question']['title_ar']}}
                                    @else
                                        {{$question['question']['title_en']}}
                                    @endif
                                </label>

                                <div>
                                    @if($lang=='ar')
                                        <?php $answers = explode(",", $question['question']['answerType']['answers_ar']) ?>
                                    @else
                                        <?php $answers = explode(",", $question['question']['answerType']['answers_en']) ?>

                                    @endif
                                    @foreach($answers as $key => $answer)
                                        <div class="checkbox-list">
                                            <label class="checkbox-inline">
                                                <input required class="checkbox-inline"
                                                       @if($question['question']['answerType']['type']==1)
                                                       type="radio"
                                                       name="answer[{{$group['group']['id']}}][{{$question['question']['id']}}]"
                                                       @else
                                                       type="checkbox"
                                                       name="answer[{{$group['group']['id']}}][{{$question['question']['id']}}][]"
                                                       @endif autocomplete="off" value="{{$key}}"> {{$answer}}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <br>

                        @endforeach
                    </div>
                @endforeach
                <div class="col-md-12 col-xs-12 surveyDivs">
                    @if($lang == 'ar')
                        {{$survey['footer_ar']}}
                    @else
                        {{$survey['footer_en']}}
                    @endif
                </div>
                <div class="col-md-12 col-xs-12 surveyDivs">
                    <input type="hidden" id="checkForm" value="2">
                    <button type="button" class="button subbutton btn btn-primary" id="saveSurvey">
                        @if($lang == 'ar')
                            إرسال
                        @else
                            Confirm
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>