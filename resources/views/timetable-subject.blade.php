{{--<span style="color: #00ccff;">{{$subject}}</span>--}}
{{--<span style="color: white;">{{$subject}}</span>--}}
@if($subject)
    <a href="#?aaa" title="{{$subject}}" >
        <img src="{{asset($icon)}}" width="70" height=50" alt="{{$subject}}">
    </a>
@endif

