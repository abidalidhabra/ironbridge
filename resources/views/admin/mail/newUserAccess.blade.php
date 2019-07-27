<!DOCTYPE html>
<html lang="en">
<head>
	<title>Email</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,600i,700,800|Roboto:400,400i,500,500i,700,900" rel="stylesheet">
</head>
<body>
<div style="width: 100%; text-align: center; margin-top: 50px;">
	<div style="width: 600px; margin: 0 auto; text-align: left;">
		
		<div style="margin-top: 9px;">
			<h3 style="margin: 0px; font-size: 28px; font-weight: 700; font-family: 'Roboto', Bold;">You are invited to join the Ironbridge console. Please <a href="{{ route('admin.setPassword',$token) }}">click here</a> for complete your profile.</h3>			
		</div>
		<div style="border-bottom: 1px solid #DCDBDB; margin-top: 40px;">
			<p style="font-family: 'Open Sans', sans-serif; font-size: 13px; color: #000; font-weight: 700;  line-height: 20px;">This email contains private information for your Ironbridge1779 account — please don’t forward it to anyone else.</p>
		</div>
		<div style="text-align: center;">
			<a href="JavaScript:Void(0)" style="display: inline-block;"><img src="{{ asset('admin_assets/images/logo.png') }}" style="width: 50px;margin-top: 20px;"></a>
		</div>
		<div style="text-align: center; color: #000000; font-family: 'Open Sans', sans-serif; font-weight: 700; margin-top: 13px; margin-bottom: 30px;">
			<p style="margin: 0px; margin-top: 10px; font-size: 14px;text-align: center;">Made by Ironbridge1779</p>
			<!-- <p style="margin-top: 5px; font-size: 14px;text-align: center;">3456 91 Street NW, Edmonton, AB T6E5R1 Canada</p> -->
		</div>
	</div>
</div>
</body>
</html>
