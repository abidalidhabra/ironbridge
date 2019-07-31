<!DOCTYPE html>
<html lang="en">
<head>
	<title>Password Reset OTP </title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,600i,700,800|Roboto:400,400i,500,500i,700,900" rel="stylesheet">
</head>
<body>
<div style="width: 100%; text-align: center; margin-top: 50px;">
	<div style="width: 600px; margin: 0 auto; text-align: left;">
		
		<div style="margin-top: 9px;">
			<h3 style="margin: 0px; font-size: 28px; font-weight: 700; font-family: 'Roboto', Bold;">Reset Your Password</h3>
			<p style="font-family: 'Open Sans', sans-serif; font-weight: 700; line-height: 20px; font-size: 13px;">You are receiving this email because we received a password reset request for your account. Your Password Reset OTP :</p>
		</div>
		<div style="border-style: dashed; border-radius: 5px; border-color: #D1D1D1; text-align: center; padding-top: 11px; padding-bottom: 10px; border-width: 2px; margin-top: 24px;">
			<p style="color: #000; font-family: 'Roboto', Bold; font-weight: 700; font-size: 29px; margin: 0px;text-align: center;">{{ substr($otp, 0, 3).'-'.substr($otp, 3, 6) }}</p>
		</div>
		<div style="border-bottom: 1px solid #DCDBDB; margin-top: 40px;">
			<p style="font-family: 'Open Sans', sans-serif; font-size: 13px; color: #000; font-weight: 700;  line-height: 20px;">This email contains private information for your ironbridge1779 account — please don’t forward it to
anyone else.</p>
		</div>
		<div style="text-align: center;">
			<a href="JavaScript:Void(0)" style="display: inline-block;"><img src="{{ asset('admin_assets/images/logo.png') }}" style="width: 50px;margin-top: 20px;"></a>
		</div>
		<div style="text-align: center; color: #000000; font-family: 'Open Sans', sans-serif; font-weight: 700; margin-top: 13px; margin-bottom: 30px;">
			<p style="margin: 0px; margin-top: 10px; font-size: 14px;text-align: center;">Made by ironbridge1779</p>
			<!-- <p style="margin-top: 5px; font-size: 14px;text-align: center;">3456 91 Street NW, Edmonton, AB T6E5R1 Canada</p> -->
		</div>
	</div>
</div>
</body>
</html>