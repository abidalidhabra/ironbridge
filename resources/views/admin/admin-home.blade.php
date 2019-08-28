@section('title','Ironbridge1779 | Dashboard')
@extends('admin.layouts.admin-app')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('admin_assets/css/daterangepicker.css') }}" /> -->
@section('content')
    
<div class="right_paddingboxpart">      
    <div class="centr_paretboxpart">
        <div class="signeup_topbox">
            <div class="signeup_lefttextbox">
                <p>Signed up</p>
            </div>
            <!-- <div class="date_textboxpart">
                <img src="{{ asset('admin_assets/images/datepicker.png') }}">
                <input type="text" name="datefilter" value="2 November, 2018 - 2 December 2018">
            </div> -->
        </div>
        <div class="signeup_innerborderbox">
            <div class="total_usersdetlis">
                <ul>
                    <li>
                        <img src="{{ asset('admin_assets/svg/user.svg') }}">
                        <h3>{{ $data['total_user'] }}</h3>
                        <p>Total Users</p>
                    </li>
                    <li>
                        <img src="{{ asset('admin_assets/svg/male-icon.svg') }}">
                        <h3>{{ $data['male'] }}</h3>
                        <p>Male</p>
                    </li>
                    <li>
                        <img src="{{ asset('admin_assets/svg/female-icon.svg') }}">
                        <h3>{{ $data['female'] }}</h3>
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
                        <img src="{{ asset('admin_assets/svg/apple-icon.svg') }}">
                        <h3>{{ $data['device_ios'] }}</h3>
                        <p>ios</p>
                    </div>
                    <div class="iosdeviceuser_text bordersetnone">
                        <img src="{{ asset('admin_assets/svg/android-icon.svg') }}">
                        <h3>{{ $data['device_android'] }}</h3>
                        <p>Android</p>
                    </div>
                </div>
                <div class="city_childbox prohntbox">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="hunt-number">{{ $data['huntProgress'] }}</p>
                            <p class="hunt-text">In Progress Hunts</p>
                        </div>
                        <div class="col-md-6">
                            <p class="hunt-number">{{ $data['huntCompleted'] }}</p>
                            <p class="hunt-text">Completed Hunts</p>
                        </div>
                    </div>
                </div>
                <div class="city_childbox">
                    
                    <div class="devicetital_text">
                        <p>Top 5 Hunts</p>
                    </div>
                    <?php
                        $i = 1;
                    ?>
                    @forelse($data['huntTop'] as $key => $hunt)
                        <div class="citycategory_box">
                            <div class="leftcity_textbox">
                                <p>{{ $hunt }}</p>
                            </div>
                            <div class="rightcity_textbox">
                                <p>{{ $key }}</p>
                            </div>
                        </div>
                        <?php
                            $i++;
                            if ($i == 6) {
                                break;
                            }
                        ?>
                    @empty
                        <div class="citycategory_box">
                            <div class="leftcity_textbox">
                                <p>No records found</p>
                            </div>
                        </div>
                    @endforelse
                    <!--<div class="citycategory_box">
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
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    <div class="right_paretboxpart">
        <div class="lifetime_titaltext">
            <!-- <p>Lifetime</p> -->
        </div>
        <div class="verified_detlisbox">
            <ul>
                <h3>Events</h3>
                <li>
                    <img src="{{ asset('admin_assets/svg/news.svg') }}">
                    <h3>{{ $data['total_event'] }}</h3>
                    <p>Total Events</p>
                </li>
                <li>
                    <img src="{{ asset('admin_assets/svg/news.svg') }}">
                    <h3>{{ $data['event_participations'] }}</h3>
                    <p>Total Participated</p>
                </li>     
            </ul>
            <ul>
                <!-- <li>
                    <img src="{{ asset('admin_assets/svg/news.svg') }}">
                    <h3>{{ $data['news'] }}</h3>
                    <p>Total News</p>
                </li> -->
                <h3>Hunts</h3>
                <li>
                    <img src="{{ asset('admin_assets/svg/earth.svg') }}">
                    <h3>{{ $data['total_country'] }}</h3>
                    <p>Total Counties</p>
                </li>
                <li>
                    <img src="{{ asset('admin_assets/svg/city-icon.svg') }}">
                    <h3>{{ $data['total_province'] }}</h3>
                    <p>Total Province</p>
                </li>
                <li>
                    <img src="{{ asset('admin_assets/svg/city-icon.svg') }}">
                    <h3>{{ $data['total_city'] }}</h3>
                    <p>Total Cities</p>
                </li>
                <li>
                    <img src="{{ asset('admin_assets/svg/map.svg') }}">
                    <h3>{{ $data['treasure_locations'] }}</h3>
                    <p>Total Treasure Locations</p>
                </li>     
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')

@endsection
