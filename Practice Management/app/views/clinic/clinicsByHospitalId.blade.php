<option value="">Choose</option>
@foreach($clinics as $val)
    <option value="{{$val['id']}}">{{$val['name']}}</option>
@endforeach