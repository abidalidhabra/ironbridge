@extends('admin.layouts.admin-app')

@section('content')
<div class="wrapper pa-0">
    <!-- Main Content -->
    <div class="page-wrapper pa-0 ma-0 auth-page">
        <div class="container-fluid">
            <!-- Row -->
            <div class="table-struct full-width full-height">
                <div class="table-cell vertical-align-middle auth-form-wrap">
                    <div class="auth-form  ml-auto mr-auto no-float">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <div class="sp-logo-wrap text-center pa-0 mb-30 setlogobox_text img">
                                    <a href="index-2.html">
                                        <img class="brand-img mr-10" src="{{ asset('admin_assets/images/logo1.png') }}" alt="brand"/ width="10%">
                                        <!-- <span class="brand-text">Ridewale</span> -->
                                        <!-- {{ __('Reset Password') }} -->
                                    </a>
                                </div>
                                <div class="mb-30 settextboxtop">
                                    <div class="signupadmin_textset">
                                        <h3 class="text-center txt-dark mb-10">Need help with your password?</h3>
                                        <h6 class="text-center txt-grey nonecase-font">Enter the email you use for Plow, and we’ll help you create a new password.</h6>
                                        @if (session('status'))
                                            <div class="alert alert-success" role="alert">
                                                {{ session('status') }}
                                            </div>
                                        @endif
                                    </div>  
                                </div>  
                                <div class="form-wrap">
                                    <div class="inputandlabel_detlisset">
                                        <form method="POST" action="{{ route('admin.password.email') }}" aria-label="{{ __('Admin Reset Password') }}" class="col-md-4 col-md-offset-4">
                                            @csrf
                                            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                                <label class="control-label mb-10" for="email">{{ __('E-Mail Address') }}s</label>
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="{{ old('email') }}" required>
                                                @if ($errors->has('email'))
                                                    <div class="help-block with-errors">
                                                        <ul class="list-unstyled">
                                                            <li>{{ $errors->first('email') }}</li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="form-group text-center btnsetcolor1 signinbuttenbox">
                                                <button type="submit" class="btn btn-info btn-rounded">{{ __('Send Password Reset Link') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Row -->   
        </div>
        
    </div>
    <!-- /Main Content -->
</div>
<!-- /#wrapper -->
@endsection
