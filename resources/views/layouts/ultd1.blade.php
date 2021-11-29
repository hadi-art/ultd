<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="{{asset('specialscooltemplate/css/style.css')}}">
    <!--[if IE 6]><link rel="stylesheet" type="text/css" href="{{asset('specialscooltemplate/css/ie6.css')}}"><![endif]-->
</head>
<body>
<div id="header">
    @php
        $route_name = Route::currentRouteName();
        $routeList = \Route::getRoutes();

        #$routeList = json_encode($routeList);
        #$routeList = json_decode($routeList,true);
        #dd($routeList);
        #for($a=0;$a<count($routeList->nameList);$a++){
        #$array_name = $routeList->nameList[$a];
        #    $current_class[$array_name]= "class='current'";
        #}
    $home_active = "";
    $lp_active = "";

    $current_active = "class='current'";
    if($route_name == 'schedule.home'){
        $home_active = $current_active;
    }
    if($route_name == 'lp.index'){
        $lp_active = $current_active;
    }
    @endphp


    <div> <a href="#"><img src="{{asset('specialscooltemplate/images/ultd-logo3.PNG')}}" alt=""></a>
        <ul>
            <li @php echo $home_active;@endphp><a href="{{route('home')}}">Home</a></li>
            <li @php echo $lp_active;@endphp><a href="{{route('lp.index')}}">Lesson Plan</a></li>
            <li><a href="#">Student</a></li>
{{--            <li @php echo $home_active;@endphp><a href="{{route('schedule.home')}}">Schedule</a></li>--}}
{{--            <li><a href="#">Hubungi</a></li>--}}
            @can('system.roles')
                <li><a href="/role">Roles & Permission</a></li>
            @endcan

            <li>
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <img width="40px" src="{{$user->icon}}" alt="Avatar" style="border-radius: 50%"/>
            </a><br><br>
                {{ucwords($user->name)}}
            </li>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

        </ul>
    </div>
</div>
<div id="content">
    @include('sweetalert::alert')

    <div>
        @yield('content')

    </div>
</div>
<div id="footer">
{{--    <div>--}}
{{--        <div> <span>Follow us</span> <a href="#" class="facebook">Facebook</a> <a href="#" class="subscribe">Subscribe</a> <a href="#" class="twitter">Twitter</a> <a href="#" class="flicker">Flickr</a> </div>--}}

{{--    </div>--}}
    <p class="footnote">Copyright &copy; 2021 <a href="#">ULTD</a> All rights reserved | Website By <a target="_blank" href="#">rpi.iot</a></p>
</div>
</body>
</html>
