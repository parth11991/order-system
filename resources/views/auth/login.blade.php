@extends('layouts.app')

@section('content')
<div class="loginBox"> 
    <img class="user" src="https://onfuro.com/images/onfuro_white.png" height="100px">
    <h3>Sign in here</h3>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="inputBox"> 
            <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
            <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
            <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
            &nbsp;<input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        </div> 
        <input class="mt-2" type="submit" name="" value="Login">
    </form> 
    @if (Route::has('password.request'))<a href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>@endif                   
    <div class="text-center">
        <p style="color: #59238F;"><a href="{{ route('register') }}">{{ __('I am a new user') }}</a></p>
    </div>
</div>
<style type="text/css">
    body{
    margin: 0;
    padding: 0;
    background: linear-gradient(to right, #b92b27, #1565c0);
    height: 100vh;
    font-family: sans-serif;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    overflow: hidden
}
@media screen and (max-width: 600px;
){
    body{
        background-size: cover;
        : fixed
    }
}
 #particles-js{
    height: 100%
}
 .loginBox{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    width: 350px;
    min-height: 200px;
    background: #000000;
    border-radius: 10px;
    padding: 40px;
    box-sizing: border-box
}
.user{
    margin: 0 auto;
    display: block;
    margin-bottom: 20px
}
h3{
    margin: 0;
    padding: 0 0 20px;
    color: #59238F;
    text-align: center
}
.loginBox input{
    width: 100%;
    margin-bottom: 20px
}
.loginBox input[type="email"], .loginBox input[type="password"]{
    border: none;
    border-bottom: 2px solid #262626;
    outline: none;
    height: 40px;
    color: #fff;
    background: transparent;
    font-size: 16px;
    padding-left: 20px;
    box-sizing: border-box
}
.loginBox input[type="email"]:hover, .loginBox input[type="password"]:hover{
    color: #42F3FA;
    border: 1px solid #42F3FA;
    box-shadow: 0 0 5px rgba(0,255,0,.3), 0 0 10px rgba(0,255,0,.2), 0 0 15px rgba(0,255,0,.1), 0 2px 0 black
}
.loginBox input[type="email"]:focus, .loginBox input[type="password"]:focus{
    border-bottom: 2px solid #42F3FA
}
.inputBox{
    position: relative
}
.inputBox span{
    position: absolute;
    top: 10px;
    color: #262626
}
.loginBox input[type="submit"]{
    border: none;
    outline: none;
    height: 40px;
    font-size: 16px;
    background: #59238F;
    color: #fff;
    border-radius: 20px;
    cursor: pointer
}
.loginBox a{
    color: #262626;
    font-size: 14px;
    font-weight: bold;
    text-decoration: none;
    text-align: center;
    display: block
}
a:hover{
    color: #00ffff
}
p{
    color: #0000ff
}
</style>
@endsection
