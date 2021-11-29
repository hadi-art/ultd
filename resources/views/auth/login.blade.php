@extends('layouts.app')

@section('content')

    <!-- Minified CSS and JS -->
    <link   rel="stylesheet"
            href="{{asset('bootstrap/css/bootstrap.min.css')}}">
    <script src="{{asset('bootstrap/js/bootstrap.min.js')}}">
    </script>

{{--    @if(Session::has('message'))--}}
{{--        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>--}}
{{--    @endif--}}


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
{{--                                <a href="{{route('auth/google')}}"><img src="{{asset('/images/googlesignin3.png')}}" width="40%" height="100%" ></a>--}}
{{--                                <a href="#"><button type="button" style="background:#4285f4; color:white; border:none; width:110px; height:37px; border-radius:3%;"><img src="https://www.iconfinder.com/data/icons/social-media-2210/24/Google-512.png" style="width:30px; background:white; border-radius:50%;" alt=""><b style="top: -2px; left: 5px; position: relative">Google Sign In</b></button></a>--}}



                                        <a class="btn btn-outline-dark" href="{{route('auth/google')}}" role="button" style="text-transform:none">
                                            <img width="20px" style="margin-bottom:3px; margin-right:5px" alt="Google sign-in" src="{{asset('/images/Google__G__Logo.svg.png')}}" />
                                            Google Sign In
                                        </a>



                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Password?') }}
                                    </a>
                                @endif

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
