@extends('admin.layouts.admin-app')
@section('title','ironbridge1779 | Login')
@section('content')
<div class="signupparent_divbox">
    <div class="signup_innerbox">
        <form method="POST" action="{{ route('admin.login') }}" aria-label="{{ __('Admin Login') }}">
            @csrf
            <div class="login_toplogobox">
                <img src="{{ asset('admin_assets/svg/ib-logo.svg') }}">
                <h3>Sign in to Admin Panel</h3>
                <p>Enter your details below</p>  
            </div>
            <div class="logindetail_inputbox">
                <div class="form-group">
                    <p>Email address</p>
                    <input type="text" name="email" class="form-control" placeholder="Enter email" value="{{ old('email') }}" required autofocus>
                    @if ($errors->has('email'))
                        <div class="help-block with-errors text-left">
                            <ul class="list-unstyled">
                                <li>{{ $errors->first('email') }}</li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
            <div class="logindetail_inputbox">
                <div class="form-group">
                    <p>Password</p>
                    <!-- <a href="javascript:void(0)">Forgot Password ?</a> -->
                    <input type="password" name="password" class="form-control" placeholder="Enter password" value="{{ old('password') }}" required autofocus>
                    @if ($errors->has('password'))
                        <div class="help-block with-errors text-left">
                            <ul class="list-unstyled">
                                <li>{{ $errors->first('password') }}</li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
            <div class="customcheck">
                <label class="checkcontainer">Keep me logged in
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="checkmark"></span>
                </label>
            </div>
            <div class="signbtn_box">        
                <button type="submit">sign in</button>
            </div>
        </form>
    </div>
</div>
@endsection
