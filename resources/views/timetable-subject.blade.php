@if($subject)
    <a href="/get_lp_slot_info?year={{$year}}&week={{$week}}" title="{{$subject}}" >
        <img src="{{asset($icon)}}" width="70" height=50" alt="{{$subject}}">
    </a>

    <button id="myBtn">Open Modal</button>
@endif



