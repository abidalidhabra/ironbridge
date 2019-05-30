@section('title','Dot Dating | Dashboard')
@extends('admin.layouts.admin-app')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('admin_assets/css/daterangepicker.css') }}" /> -->
@section('content')
    
<div class="right_paddingboxpart">      
    <div class="centr_paretboxpart">
        <div class="signeup_topbox">
            <div class="signeup_lefttextbox">
                <p>Signed up</p>
            </div>
            <div class="date_textboxpart">
                <img src="{{ asset('admin_assets/images/datepicker.png') }}">
                <input type="text" name="datefilter" value="2 November, 2018 - 2 December 2018">
            </div>
        </div>
        <div class="signeup_innerborderbox">
            <div class="total_usersdetlis">
                <ul>
                    <li>
                        <img src="{{ asset('admin_assets/images/Users.png') }}">
                        <h3>55,000</h3>
                        <p>Total Users</p>
                    </li>
                    <li>
                        <img src="{{ asset('admin_assets/images/Users.png') }}">
                        <h3>55,000</h3>
                        <p>Male</p>
                    </li>
                    <li>
                        <img src="{{ asset('admin_assets/images/Users.png') }}">
                        <h3>55,000</h3>
                        <p>Female</p>
                    </li>
                </ul>
            </div>
            <div class="deviceandcity_paretpart">
                <div class="device_childbox">
                    <div class="devicetital_text">
                        <p>Device</p>
                    </div>
                    <div class="iosdeviceuser_text">
                        <h3>25,000</h3>
                        <p>ios</p>
                    </div>
                    <div class="iosdeviceuser_text bordersetnone">
                        <h3>25,000</h3>
                        <p>Android</p>
                    </div>
                </div>
                <div class="city_childbox">
                    <div class="devicetital_text">
                        <p>City</p>
                    </div>
                    <div class="citycategory_box">
                        <div class="leftcity_textbox">
                            <p>Abc</p>
                        </div>
                        <div class="rightcity_textbox">
                            <p>92,333(42%)</p>
                        </div>
                    </div>
                    <div class="citycategory_box">
                        <div class="leftcity_textbox">
                            <p>Abc</p>
                        </div>
                        <div class="rightcity_textbox">
                            <p>92,333(42%)</p>
                        </div>
                    </div>
                    <div class="citycategory_box">
                        <div class="leftcity_textbox">
                            <p>Abc</p>
                        </div>
                        <div class="rightcity_textbox">
                            <p>92,333(42%)</p>
                        </div>
                    </div>
                    <div class="citycategory_box">
                        <div class="leftcity_textbox">
                            <p>Abc</p>
                        </div>
                        <div class="rightcity_textbox">
                            <p>92,333(42%)</p>
                        </div>
                    </div>
                    <div class="citycategory_box cityborderset">
                        <div class="leftcity_textbox">
                            <p>Abc</p>
                        </div>
                        <div class="rightcity_textbox">
                            <p>92,333(42%)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="right_paretboxpart">
        <div class="lifetime_titaltext">
            <p>Lifetime</p>
        </div>
        <div class="verified_detlisbox">
            <ul>
                <li>
                    <img src="{{ asset('admin_assets/images/email.png') }}">
                    <h3>30,000</h3>
                    <p>Email Addresses Verified</p>
                </li>
                <li>
                    <img src="{{ asset('admin_assets/images/phone.png') }}">
                    <h3>30,000</h3>
                    <p>Phone Numbers Verified</p>
                </li>         
                <li>
                    <img src="{{ asset('admin_assets/images/instagram.png') }}">
                    <h3>30,000</h3>
                    <p>Instagram Connections</p>
                </li>
                <li>
                    <img src="{{ asset('admin_assets/images/youtub.png') }}">
                    <h3>30,000</h3>
                    <p>Total Ads Watched</p>
                </li>         
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')

@endsection
