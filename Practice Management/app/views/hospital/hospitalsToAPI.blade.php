@if(isset($lang) && $lang == 'ar')
    <option value="">إختر</option>
@else
    <option value="">Choose</option>
@endif
@foreach($hospitals as $val)
    @if(isset($lang))
        @if($lang == 'ar')
            <option value="{{$val['id']}}">{{$val['name_ar']}}</option>
        @else
            <option value="{{$val['id']}}">{{$val['name']}}</option>
        @endif

    @else
        <option value="{{$val['id']}}">{{$val['name']}}</option>
    @endif
@endforeach