@section('title','Ironbridge1779 | Dashboard')
@extends('admin.layouts.admin-app')
@section('styles')

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css" />
@endsection('styles')

@section('content')
    
<div class="right_paddingboxpart">      
    <div class="centr_paretboxpart">
        <div class="signeup_topbox">
            <div class="signeup_lefttextbox">
                <p>Signed up</p>
            </div>
            <div class="date_textboxpart">
                <form method="post" id="daterangepickerForm">
                    @csrf
                    <img src="{{ asset('admin_assets/images/datepicker.png') }}">
                    <input type="text" name="date" value="" />
                </form>
            </div>
        </div>
        <div class="signeup_innerborderbox">
            <div class="total_usersdetlis">
                <ul>
                    <li>
                        <img src="{{ asset('admin_assets/svg/user.svg') }}">
                        <h3 id="total_user">{{ $data['total_user'] }}</h3>
                        <p>Total Users</p>
                    </li>
                    <li>
                        <img src="{{ asset('admin_assets/svg/male-icon.svg') }}">
                        <h3 id="male">{{ $data['male'] }}</h3>
                        <p>Male</p>
                    </li>
                    <li>
                        <img src="{{ asset('admin_assets/svg/female-icon.svg') }}">
                        <h3 id="female">{{ $data['female'] }}</h3>
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
                        <h3 id="device_ios">{{ $data['device_ios'] }}</h3>
                        <p>ios</p>
                    </div>
                    <div class="iosdeviceuser_text bordersetnone">
                        <img src="{{ asset('admin_assets/svg/android-icon.svg') }}">
                        <h3 id="device_android">{{ $data['device_android'] }}</h3>
                        <p>Android</p>
                    </div>
                </div>
                <div class="city_childbox prohntbox">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="hunt-number" id="hunt_progress">{{ $data['huntProgress'] }}</p>
                            <p class="hunt-text">In Progress Hunts</p>
                        </div>
                        <div class="col-md-6">
                            <p class="hunt-number" id="hunt_completed">{{ $data['huntCompleted'] }}</p>
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
                    <div id="hunt_top_city">
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
                    </div>
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
                <h3>Payment</h3>
                <li>
                    <img src="{{ asset('admin_assets/svg/news.svg') }}">
                    <a href="{{ route('admin.payment.index') }}">
                        <h3>${{ $data['total_payment'] }}</h3>
                        <p>Total Payment</p>
                    </a>
                </li>
            </ul>
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.js"></script>
    <script type="text/javascript">
        $( document ).ready(function() {
            var startDate = "{{ $data['last_record_date']->format('d M Y') }}";
            var endDate = "{{ $data['first_record_date']->format('d M Y') }}";
            $('input[name="date"]').daterangepicker({ 
                maxDate: new Date(),
                startDate: endDate,
                endDate: startDate,
                locale: {
                    format: 'DD MMM YYYY',
                }
            });


            $('input[name="date"]').change(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "GET",
                    url: '{{ route("admin.signedUpDateFilter") }}',
                    data: $('#daterangepickerForm').serialize(),
                    beforeSend: function() {    
                    },
                    success: function(response)
                    {
                        if (response.status == true) {
                            $.each(response.data,function(index , value){
                                $('#'+index).text(value);
                            });

                            $('#hunt_top_city').html('');
                            let huntTopCity = response.data['huntTop'];
                            if(typeof huntTopCity != 'undefined' && huntTopCity != ""){
                                $.each(response.data['huntTop'],function(index , value){
                                    $('#hunt_top_city').prepend(`<div class="citycategory_box">
                                                                    <div class="leftcity_textbox">
                                                                        <p>`+value+`</p>
                                                                    </div>
                                                                    <div class="rightcity_textbox">
                                                                        <p>`+index+`</p>
                                                                    </div>
                                                                </div>`);
                                });
                            } else {
                                $('#hunt_top_city').append(`<div class="citycategory_box">
                                                            <div class="leftcity_textbox">
                                                                <p>No records found</p>
                                                            </div>
                                                        </div>`);
                            }
                        }
                    }
                });
            });
        });
    </script>
@endsection
