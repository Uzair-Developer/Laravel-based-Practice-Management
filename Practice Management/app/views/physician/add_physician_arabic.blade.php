{{Form::open(array('role'=>"form",'url' => $formAction))}}
<div class="table-responsive">
    <table class="table table-bordered col-md-12">
        <thead>
        <tr>
            <th style="width: 15px">#</th>
            <th>Full Name En</th>
            <th>First Name Ar</th>
            <th>Last Name Ar</th>
            <th>Bookable?</th>
            <th>In Report</th>
            <th>Revisit Limit (Days)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($insertedPhysicians as $val)
            <tr>
                <td>
                    {{$val['id']}}
                    @if(isset($activateAction) && $activateAction)
                        <input type="hidden" name="id" value="{{$val['id']}}">
                    @else
                        <input type="hidden" name="id[]" value="{{$val['id']}}">
                    @endif
                </td>
                <td>
                    <div style="width: 250px;">{{$val['full_name']}}</div>
                </td>
                <td>
                    @if(isset($activateAction) && $activateAction)
                        <input required type="text" name="first_name_ar" value="{{$val['first_name_ar']}}">
                    @else
                        <input required type="text" name="first_name_ar[]">
                    @endif
                </td>
                <td>
                    @if(isset($activateAction) && $activateAction)
                        <input required type="text" name="last_name_ar" value="{{$val['last_name_ar']}}">
                    @else
                        <input required type="text" name="last_name_ar[]">
                    @endif
                </td>
                <td>
                    @if(isset($activateAction) && $activateAction)
                        <select required name="bookable">
                            <option @if($val['bookable'] == 1) selected @endif value="1">Bookable</option>
                            <option @if($val['bookable'] == 2) selected @endif value="2">Not Bookable</option>
                        </select>
                    @else
                        <select required name="bookable[]">
                            <option value="1">Bookable</option>
                            <option value="2">Not Bookable</option>
                        </select>
                    @endif

                </td>
                <td>
                    @if(isset($activateAction) && $activateAction)
                        <select required name="in_report">
                            <option @if($val['in_report'] == 1) selected @endif value="1">Yes</option>
                            <option @if($val['in_report'] == 2) selected @endif value="2">No</option>
                        </select>
                    @else
                        <select required name="in_report[]">
                            <option value="1">Yes</option>
                            <option value="2">No</option>
                        </select>
                    @endif
                </td>
                <td>
                    @if(isset($activateAction) && $activateAction)
                        <input type="text" name="revisit_limit" value="{{$val['revisit_limit']}}">
                    @else
                        <input type="text" name="revisit_limit[]">
                    @endif
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<br>
<button type="submit" class="btn btn-primary">Save</button>
{{Form::close()}}