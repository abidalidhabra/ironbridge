@section('title','Ironbridge1779 | Analytics Metrics')
@extends('admin.layouts.admin-app')
@section('styles')

   
@endsection('styles')

@section('content')
    
<div class="right_paddingboxpart">      
    <div class="centr_paretboxpart analytmetri_coverbox">
        <div class="signeup_topbox">
            <div class="signeup_lefttextbox">
                <p>Analytics Metrics</p>
            </div>
            <!-- <div class="date_textboxpart">
                <form method="post" id="storeDaterangepickerForm">
                    @csrf
                    <img src="{{ asset('admin_assets/images/datepicker.png') }}">
                    <input type="text" name="store_date" value="" />
                </form>
            </div> -->
        </div>
        <div class="signeup_innerborderbox">
            <div class="total_usersdetlis">
                <ul>
                    <div class="titleaddatep">
                        <div class="titleboxleft">
                            <h4>Store</h4>
                        </div>
                        <div class="daterightbox">
                            <form method="post" id="storeDaterangepickerForm">
                                @csrf
                                <img src="{{ asset('admin_assets/images/datepicker.png') }}">
                                <input type="text" name="store_date" value="" />
                            </form>
                            <!-- <a href="javascript:void(0)" id="refresh_store" data-action="refresh" data-date="{{ $data['plan_purchase_start_date']->format('d M Y').' - '.$data['plan_purchase_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a> -->
                        </div>
                        <div class="refreshbox">
                            <a href="javascript:void(0)" id="refresh_store" data-action="refresh" data-date="{{ $data['plan_purchase_start_date']->format('d M Y').' - '.$data['plan_purchase_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a>
                        </div>
                    </div>
                    
                    <li>
                        <h3 id="total_coins_purchase">{{ $data['total_coins_purchase'] }}</h3>
                        <p>Total coins purchased</p>
                    </li>
                    <li>
                        <h3 id="average_revenue">{{ $data['average_revenue'] }}</h3>
                        <p>Average revenue</p>
                    </li>
                    <li>
                        <h3 id="average_skeleton_keys_purchased">{{ $data['average_skeleton_keys_purchased'] }}</h3>
                        <p>Average skeleton keys purchased</p>
                    </li>
                    <li>
                        <h3 id="total_amount_skeleton_keys_purchased">{{ $data['total_amount_skeleton_keys_purchased'] }}</h3>
                        <p>Total amount skeleton keys purchased</p>
                    </li>
                    <li>
                        <h3 id="total_revenue_google_fees">{{ $data['total_revenue_google_fees'] }}</h3>
                        <p>Total revenue google fees</p>
                    </li>
                    <li>
                        <h3 id="total_revenue_apple_fees">{{ $data['total_revenue_apple_fees'] }}</h3>
                        <p>Total revenue apple fees</p>
                    </li>
                </ul>
            </div>
            <div class="total_usersdetlis">
                <ul>
                    <div class="titleaddatep">
                        <div class="titleboxleft">
                            <h4>User</h4>
                        </div>
                        <div class="daterightbox">
                            <form method="post" id="userDaterangepickerForm">
                                @csrf
                                <img src="{{ asset('admin_assets/images/datepicker.png') }}">
                                <input type="text" name="user_date" value="" />
                            </form>
                            <!-- <a href="javascript:void(0)" id="refresh_user" data-action="refresh" data-date="{{ $data['user_start_date']->format('d M Y').' - '.$data['user_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a> -->
                        </div>
                        <div class="refreshbox">
                            <a href="javascript:void(0)" id="refresh_user" data-action="refresh" data-date="{{ $data['user_start_date']->format('d M Y').' - '.$data['user_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a>
                        </div>
                    </div>
                    <li>
                        <h3 id="per_male">{{ $data['per_male'] }}</h3>
                        <p>Total percentage of total male</p>
                    </li>
                    <li>
                        <h3 id="per_female">{{ $data['per_female'] }}</h3>
                        <p>Total percentage of total female</p>
                    </li>
                    <div class="titleaddatep">
                        <div class="titleboxleft">
                            <h4>Avatar Item</h4>
                        </div>
                    </div>
                    <li>
                        <h3 id="average_avatar_items_purchased">{{ $data['average_avatar_items_purchased'] }}</h3>
                        <p>Average avatar items purchased</p>
                    </li>
                    <li>
                        <h3 id="total_paid_avatar">{{ round($data['total_paid_avatar']) }}</h3>
                        <p>Total paid avatar</p>
                    </li>

                    <br/>
                </ul>
                <div class="avt_itembox" id="avatar_image">
                    <ul>
                    <!-- @foreach($data['total_items_purchased'] as $key => $item)
                        @if (file_exists(public_path('admin_assets/widgets/'.$key.'.png')))
                            
                        <li>                            
                            <img src="{{ asset('admin_assets/widgets/'.$key.'.png') }}">
                            <h5>Total Used : {{ $item }}</h5>                            
                        </li>                            
                            
                        @endif
                    @endforeach -->
                    </ul>
                </div>
            </div>
            <!-- <div class="total_usersdetlis">
                <ul>
                    <h4>Users</h4>
                    <li>
                        <h3 id="per_male">{{ $data['per_male'] }}</h3>
                        <p>Total percentage of total male</p>
                    </li>
                    <li>
                        <h3 id="per_female">{{ $data['per_female'] }}</h3>
                        <p>Total percentage of total female</p>
                    </li>
                </ul>
            </div> -->
            <div class="total_usersdetlis">
                <ul>
                    <!-- <h4>Hunts</h4> -->
                    <div class="titleaddatep">
                        <div class="titleboxleft">
                            <h4>Hunts</h4>
                        </div>
                        <div class="daterightbox">
                            <form method="post" id="huntDaterangepickerForm">
                                @csrf
                                <img src="{{ asset('admin_assets/images/datepicker.png') }}">
                                <input type="text" name="hunt_date" value="" />
                            </form>
                            <!-- <a href="javascript:void(0)" id="refresh_hunt" data-action="refresh" data-date="{{ $data['hunt_user_start_date']->format('d M Y').' - '.$data['hunt_user_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a> -->
                        </div>
                        <div class="refreshbox">
                            <a href="javascript:void(0)" id="refresh_hunt" data-action="refresh" data-date="{{ $data['hunt_user_start_date']->format('d M Y').' - '.$data['hunt_user_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a>
                        </div>
                    </div>
                    <li>
                        <h3 id="per_completed_hunt">{{ $data['per_completed_hunt'] }}</h3>
                        <p>Percentage completed hunts</p>
                    </li>
                    <li>
                        <h3 id="per_completed_hunt_user">{{ $data['per_completed_hunt_user'] }}</h3>
                        <p>Percentage of users complete a hunt</p>
                    </li>
                    <li>
                        <h3 id="average_hunts_completed">{{ $data['average_hunts_completed'] }}</h3>
                        <p>Average hunts completed</p>
                    </li>
                </ul>
                <ul>
                    <li>
                        <h3 id="user_clue_1">{{ $data['user_clue_1'] }}</h3>
                        <p>User of Clue #1 in a hunt</p>
                    </li>
                    <li>
                        <h3 id="user_clue_2">{{ $data['user_clue_2'] }}</h3>
                        <p>User of Clue #2 in a hunt</p>
                    </li>
                    <li>
                        <h3 id="user_clue_3">{{ $data['user_clue_3'] }}</h3>
                        <p>User of Clue #3 in a hunt</p>
                    </li>
                </ul>
                <ul>
                    <li>
                        <h3 id="user_clue_today_1">{{ $data['user_clue_today_1'] }}</h3>
                        <p>Clue 1 Not complete at least 24 hours later</p>
                    </li>
                    <li>
                        <h3 id="user_clue_today_2">{{ $data['user_clue_today_2'] }}</h3>
                        <p>Clue 2 Not complete at least 24 hours later</p>
                    </li>
                    <li>
                        <h3 id="user_clue_today_3">{{ $data['user_clue_today_3'] }}</h3>
                        <p>Clue 3 Not complete at least 24 hours later</p>
                    </li>
                </ul>
                <ul id="complated_clue_day">
                    @foreach ($data['hunt_complted_clue'] as $key => $value)
                        <li>
                            <h3>{{ number_format(($value/$data['total_hunt_complated'])*100,2) }}%</h3>
                            <p>Per clue complete day {{ $key+1 }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="total_usersdetlis">
                <ul>
                    <!-- <h4>Event</h4> -->
                    <div class="titleaddatep">
                        <div class="titleboxleft">
                            <h4>Events</h4>
                        </div>
                        <div class="daterightbox">
                            <form method="post" id="eventsDaterangepickerForm">
                                @csrf
                                <img src="{{ asset('admin_assets/images/datepicker.png') }}">
                                <input type="text" name="event_date" value="" />
                            </form>
                            <!-- <a href="javascript:void(0)" id="refresh_event" data-action="refresh" data-date="{{ $data['event_user_start_date']->format('d M Y').' - '.$data['event_user_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a> -->
                        </div>
                        <div class="refreshbox">
                            <a href="javascript:void(0)" id="refresh_event" data-action="refresh" data-date="{{ $data['event_user_start_date']->format('d M Y').' - '.$data['event_user_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a>
                        </div>
                    </div>
                    <li>
                        <h3 id="user_event_city">{{ round($data['user_event_city']) }}</h3>
                        <p>User event city</p>
                    </li>
                    <li>
                        <h3 id="user_event_country">{{ round($data['user_event_country']) }}</h3>
                        <p>User event country</p>
                    </li>
                    <li>
                        <h3 id="amount_revenue_event_paid_coins">{{ $data['amount_revenue_event_paid_coins'] }}</h3>
                        <p>Amount revenue event paid coins</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
</div>
@endsection

@section('scripts')
   
    <script type="text/javascript">
        $(document).ready(function() {
            
            /* STORE */
            var planPurchaseStartDate =  "{{ $data['plan_purchase_start_date']->format('d M Y') }}";
            var planPurchaseEndDate = "{{ $data['plan_purchase_end_date']->format('d M Y') }}";
            
            storeDaterangepicker();

            function storeDaterangepicker(){
                $('input[name="store_date"]').daterangepicker({ 
                    maxDate: new Date(),
                    startDate: planPurchaseStartDate,
                    endDate: planPurchaseEndDate,
                    locale: {
                        format: 'DD MMM YYYY',
                    }
                });
            }

            $('#refresh_store').click(function(){
                var value = 'refresh_store';
                storeDateFilter(value);                
            });
            
            $('input[name="store_date"]').change(function(e) {
                e.preventDefault();
                var value = 'daterangepicker';
                storeDateFilter(value);                
            });
            

            function storeDateFilter(value){
                if (value == 'refresh_store') {
                    var data = {"store_date":$('#refresh_store').attr('data-date')};
                } else {
                    data = $('#storeDaterangepickerForm').serialize();
                }
                $.ajax({
                    type: "GET",
                    url: '{{ route("admin.getStoreDateFilter") }}',
                    data: data,
                    beforeSend: function() { 
                        if (value == 'refresh_store') {
                            $('#refresh_store i').addClass('fa-spin');
                            storeDaterangepicker();    
                        }
                        $('#refresh_store').parents('.total_usersdetlis').css('opacity','0.5');    

                    },
                    success: function(response)
                    {
                        $('#refresh_store').parents('.total_usersdetlis').css('opacity','1');
                        $('#refresh_store i').removeClass('fa-spin');    
                        if (response.status == true) {
                            $.each(response.data,function(index , value){
                                $('#'+index).text(value);
                            });
                        }
                    }
                });
            }
            /* END STORE */

            /* USER DTAE RANGE FILETR */
            var userStartDate =  "{{ $data['user_start_date']->format('d M Y') }}";
            var userEndDate = "{{ $data['user_end_date']->format('d M Y') }}";

            userDaterangepicker();
            function userDaterangepicker(){
                $('input[name="user_date"]').daterangepicker({ 
                    maxDate: new Date(),
                    startDate: userStartDate,
                    endDate: userEndDate,
                    locale: {
                        format: 'DD MMM YYYY',
                    }
                });
            }

            userDateFilter('refresh_user');

            $('#refresh_user').click(function(){
                var value = 'refresh_user';
                userDateFilter(value);                
            });

            $('input[name="user_date"]').change(function(e) {
                e.preventDefault();
                var value = 'daterangepicker';
                userDateFilter(value);   
            });

            function userDateFilter(value){
                if (value == 'refresh_user') {
                    var data = {"user_date":$('#refresh_user').attr('data-date')};
                } else {
                    data = $('#userDaterangepickerForm').serialize();
                }
                $.ajax({
                    type: "GET",
                    url: '{{ route("admin.getUserDateFilter") }}',
                    data: data,
                    beforeSend: function() {
                        if (value == 'refresh_user') {
                            $('#refresh_user i').addClass('fa-spin');    
                            userDaterangepicker();
                        }
                        $('#refresh_user').parents('.total_usersdetlis').css('opacity','0.5');    
                    },
                    success: function(response)
                    {
                        $('#refresh_user i').removeClass('fa-spin');    
                        $('#refresh_user').parents('.total_usersdetlis').css('opacity','1');    
                        if (response.status == true) {
                            $.each(response.data,function(index , value){
                                $('#'+index).text(value);
                            });
                            $('#avatar_image ul').html('');
                            $.each(response.data.total_items_purchased,function(index , value){
                                /*var image = index+'.png';
                                var image_url = '{{ asset("admin_assets/widgets/") }}'+'/'+image;    
                                var responseData = jQuery.ajax({
                                    url: image_url,
                                    type: 'HEAD',
                                    async: false
                                }).status;*/

                                $('#avatar_image ul').append(`<li><img src="`+value['image']+`"><h5>Total Used : `+value['total_use']+`</h5></li>`);
                            });
                        }
                    }
                });
            }
            /* END USER DTAE RANGE FILETR */


            /* HUNT DATE RANGE FILTER */
            var huntUserStartDate =  "{{ $data['hunt_user_start_date']->format('d M Y') }}";
            var huntUserEndDate = "{{ $data['hunt_user_end_date']->format('d M Y') }}";
            
            huntDaterangepicker();
            function huntDaterangepicker(){
                $('input[name="hunt_date"]').daterangepicker({ 
                    maxDate: new Date(),
                    startDate: huntUserStartDate,
                    endDate: huntUserEndDate,
                    locale: {
                        format: 'DD MMM YYYY',
                    }
                });
            }

            $('#refresh_hunt').click(function(){
                var value = 'refresh_hunt';
                huntDateFilter(value);                
            });

            $('input[name="hunt_date"]').change(function(e) {
                e.preventDefault();
                var value = 'daterangepicker';
                huntDateFilter(value);
            });

            function huntDateFilter(value){
                if (value == 'refresh_hunt') {
                    var data = {"hunt_date":$('#refresh_hunt').attr('data-date')};
                } else {
                    data = $('#huntDaterangepickerForm').serialize();
                }
                $.ajax({
                    type: "GET",
                    url: '{{ route("admin.getHuntDateFilter") }}',
                    data: data,
                    beforeSend: function() {
                        if (value == 'refresh_hunt') {
                            $('#refresh_hunt i').addClass('fa-spin');
                            huntDaterangepicker();    
                        }
                        $('#refresh_hunt').parents('.total_usersdetlis').css('opacity','0.5');
                    },
                    success: function(response)
                    {
                        $('#refresh_hunt').parents('.total_usersdetlis').css('opacity','1');
                        $('#refresh_hunt i').removeClass('fa-spin');    
                        if (response.status == true) {
                            $.each(response.data,function(index , value){
                                $('#'+index).text(value);
                            });

                             $('#complated_clue_day').html('');
                            $.each(response.data.hunt_complted_clue,function(index , value){
                                $('#complated_clue_day').append(`<li>
                                                                    <h3>`+((value/response.data.total_hunt_complated)*100).toFixed(2)+`%</h3>
                                                                    <p>Per clue complete day `+(parseInt(index)+1)+`</p>
                                                                </li>`);
                            });
                            
                        }
                    }
                });
            }
            /* END HUNT DATE RANGE FILTER */

            /* EVENT DATE RANGE FILTER */
            var eventStartDate =  "{{ $data['event_user_start_date']->format('d M Y') }}";
            var eventEndDate = "{{ $data['event_user_end_date']->format('d M Y') }}";
            eventDaterangepicker();
            function eventDaterangepicker(){
                $('input[name="event_date"]').daterangepicker({ 
                    maxDate: new Date(),
                    startDate: eventStartDate,
                    endDate: eventEndDate,
                    locale: {
                        format: 'DD MMM YYYY',
                    }
                });
            }

            $('#refresh_event').click(function(){
                var value = 'refresh_event';
                eventDateFilter(value);                
            });

            $('input[name="event_date"]').change(function(e) {
                e.preventDefault();
                var value = 'daterangepicker';
                eventDateFilter(value);
            });

            function eventDateFilter(value){
                if (value == 'refresh_event') {
                    var data = {"event_date":$('#refresh_event').attr('data-date')};
                } else {
                    data = $('#eventsDaterangepickerForm').serialize();
                }
                $.ajax({
                    type: "GET",
                    url: '{{ route("admin.getEventDateFilter") }}',
                    data: data,
                    beforeSend: function() {
                        if (value == 'refresh_event') {
                            $('#refresh_event i').addClass('fa-spin');    
                            eventDaterangepicker();
                        }
                        $('#refresh_event').parents('.total_usersdetlis').css('opacity','0.5');
                    },
                    success: function(response)
                    {
                        $('#refresh_event').parents('.total_usersdetlis').css('opacity','1');
                        $('#refresh_event i').removeClass('fa-spin');    
                        if (response.status == true) {
                            $.each(response.data,function(index , value){
                                $('#'+index).text(value);
                            });
                        }
                    }
                });
            }
            /* END EVENT DATE RANGE FILTER */
        });       
    </script>
@endsection
