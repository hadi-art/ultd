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
    <div> <a href="index.html"><img src="{{asset('specialscooltemplate/images/ultd-logo3.PNG')}}" alt=""></a>
        <ul>
            <li class="current"><a href="index.html">Utama</a></li>
            <li><a href="#">Guru</a></li>
            <li><a href="#">Murid</a></li>
            <li><a href="#">Jadual</a></li>
{{--            <li><a href="#">Hubungi</a></li>--}}

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
