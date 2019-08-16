@extends('admin.layouts.admin-app')
@section('title','Ironbridge1779 | Login')
@section('content')
<div class="signupparent_divbox">
    <div class="signup_innerbox">
        <form method="POST" id="sevePasswordFrm" action="{{ route('admin.savePassword',$data->admin['_id']) }}">
            @csrf
            <div class="login_toplogobox">
                <img src="{{ asset('admin_assets/svg/ib-logo.svg') }}">
                <h3>Set Your Account Password</h3>
                <p>Enter your details below</p>  
            </div>
            <div class="logindetail_inputbox">
                <div class="form-group">
                    <p>Email address</p>
                    <input type="text" name="email" class="form-control" placeholder="Enter email" value="{{ $data->admin['email'] }}" disabled>
                </div>
            </div>
            <input type="hidden" name="tokenData" value="{{$data->token}}">
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
            <div class="signbtn_box">        
                <button type="submit">Set Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
   $(document).ready(function() {
     $('#sevePasswordFrm').submit(function(e) {
        alert();
        e.preventDefault();
    })
     .validate({
        focusInvalid: false, 
        ignore: "",
        rules: {
            password: { required: true },
        },
        submitHandler: function (form) {
            var formData = new FormData(form);
            var id  = $('#admin_id').val();
            var url ="{{ route('admin.savePassword',$data->admin['_id']) }}";
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                processData:false,
                cache:false,
                contentType: false,
                success: function(response)
                {
                    if (response.status == true) {
                        toastr.success(response.message);
                    // location.replace('{{ route("admin.login") }}');
                } else {
                    toastr.warning(response.message);
                }
            }
        });
        }
    });
 });
</script>
@endsection