@section('title','Ironbridge1779 | Analytics')
@extends('admin.layouts.admin-app')
@section('styles')

   
@endsection('styles')

@section('content')
    
<div class="right_paddingboxpart">      
    <div class="centr_paretboxpart analytmetri_coverbox">
        <div class="signeup_topbox">
            <div class="signeup_lefttextbox">
                <p>Analytics</p>
            </div>
        </div>
        <div class="signeup_innerborderbox">
            <!-- USER -->
            <div class="total_usersdetlis">
                <ul>
                    <div class="titleaddatep">
                        <div class="titleboxleft">
                            <h4>Users</h4>
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
                        <p>Total percentage of total male <span data-toggle="tooltip" title="Total number of male avatars">?</span></p>
                    </li>
                    <li>
                        <h3 id="per_female">{{ $data['per_female'] }}</h3>
                        <p>Total percentage of total female <span data-toggle="tooltip" title="Total number of female avatars">?</span></p>
                    </li>
                    <li>
                        <h3>Pending</h3>
                        <p>Total used random mode <span data-toggle="tooltip" title="Total number of users who have used random mode">?</span></p>
                    </li>
                    <li>
                        <h3>Pending</h3>
                        <p>Percentage use random mode <span data-toggle="tooltip" title="Percentage of users that use the random mode">?</span></p>
                    </li>
                    <li>
                        <h3>Pending</h3>
                        <p>Number of users collectable <span data-toggle="tooltip" title="Number of users that have found each collectable">?</span></p>
                    </li>
                    <li>
                        <h3>Pending</h3>
                        <p>Total number of collectibles <span data-toggle="tooltip" title="Total number of collectibles for each unique user ie collected/total available (with percentage as well) – will want to know which user collected all 9/10 first in each country/province/city">?</span></p>
                    </li>
                </ul>
            </div>
            <!-- END USER -->

            <!-- HUNTS -->
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
                        </div>
                        <div class="refreshbox">
                            <a href="javascript:void(0)" id="refresh_hunt" data-action="refresh" data-date="{{ $data['hunt_user_start_date']->format('d M Y').' - '.$data['hunt_user_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a>
                        </div>
                    </div>
                    <li>
                        <h3 id="per_completed_hunt">{{ $data['per_completed_hunt'] }}</h3>
                        <p>Percentage completed hunts <span data-toggle="tooltip" title="Number of unique users that have completed hunts (with percentage of total users)">?</span></p>
                    </li>
                    <li>
                        <h3 id="per_completed_hunt_user">{{ $data['per_completed_hunt_user'] }}</h3>
                        <p>Percentage of users complete a hunt <span data-toggle="tooltip" title="Percentage of users that complete a hunt after starting clue #1">?</span></p>
                    </li>
                    <li>
                        <h3 id="average_hunts_completed">{{ $data['average_hunts_completed'] }}</h3>
                        <p>Average hunts completed <span data-toggle="tooltip" title="Average number of hunts completed (per unique user)">?</span></p>
                    </li>
                </ul>
                <ul>
                    <li>
                        <h3 id="user_clue_1">{{ $data['user_clue_1'] }}</h3>
                        <p>User of Clue #1 in a hunt <span data-toggle="tooltip" title="Number of users on Clue #1 in a hunt">?</span></p>
                    </li>
                    <li>
                        <h3 id="user_clue_2">{{ $data['user_clue_2'] }}</h3>
                        <p>User of Clue #2 in a hunt <span data-toggle="tooltip" title="Number of users on Clue #2 in a hunt">?</span></p>
                    </li>
                    <li>
                        <h3 id="user_clue_3">{{ $data['user_clue_3'] }}</h3>
                        <p>User of Clue #3 in a hunt <span data-toggle="tooltip" title="Number of users on Clue #3 in a hunt">?</span></p>
                    </li>
                </ul>
                <ul>
                    <li>
                        <h3 id="user_clue_today_1">{{ $data['user_clue_today_1'] }}</h3>
                        <p>Clue 1 Not complete at least 24 hours <span data-toggle="tooltip" title="Number of users that have abandoned Clue #1 in a hunt (started but did not complete at least 24 hours later)">?</span></p>
                    </li>
                    <li>
                        <h3 id="user_clue_today_2">{{ $data['user_clue_today_2'] }}</h3>
                        <p>Clue 2 Not complete at least 24 hours <span data-toggle="tooltip" title="Number of users that have abandoned Clue #2 in a hunt (started but did not complete at least 24 hours later)">?</span></p>
                    </li>
                    <li>
                        <h3 id="user_clue_today_3">{{ $data['user_clue_today_3'] }}</h3>
                        <p>Clue 3 Not complete at least 24 hours <span data-toggle="tooltip" title="Number of users that have abandoned Clue #3 in a hunt (started but did not complete at least 24 hours later)">?</span></p>
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
            <!-- END HUNTS -->

            <!-- MINI GAME -->
            <div class="total_usersdetlis">
                <ul>
                    <div class="titleaddatep">
                        <div class="titleboxleft">
                            <h4>Mini Games</h4>
                        </div>
                        <div class="daterightbox">
                            <form method="post" id="eventsDaterangepickerForm">
                                @csrf
                                <img src="{{ asset('admin_assets/images/datepicker.png') }}">
                                <input type="text" name="event_date" value="" />
                            </form>
                        </div>
                        <div class="refreshbox">
                            <a href="javascript:void(0)" id="refresh_event" data-action="refresh" data-date="{{ $data['event_user_start_date']->format('d M Y').' - '.$data['event_user_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a>
                        </div>
                    </div>
                    <li>
                        <h3>Pending</h3>
                        <p>Total  favourites mini game <span data-toggle="tooltip" title="Total number of favourites for each mini game (when the favourite feature is implemented)">?</span></p>
                    </li>
                    <li>
                        <h3>Pending</h3>
                        <p>Average number of skips Random mode <span data-toggle="tooltip" title="Total and average number of skips in Random mode (for each mini game)">?</span></p>
                    </li>
                    <li>
                        <h3>Pending</h3>
                        <p>Average played random mode <span data-toggle="tooltip" title="Average number of mini games played in random mode (how many games played in each session on average)">?</span></p>
                    </li>
                    <li>
                        <h3>Pending</h3>
                        <p>Average games played random mode <span data-toggle="tooltip" title="Total and average number of games played in random mode">?</span></p>
                    </li>
                </ul>
            </div>
            <!-- END MINI GAME -->

            <!-- STORE -->
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
                        </div>
                        <div class="refreshbox">
                            <a href="javascript:void(0)" id="refresh_store" data-action="refresh" data-date="{{ $data['plan_purchase_start_date']->format('d M Y').' - '.$data['plan_purchase_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a>
                        </div>
                    </div>
                    
                    <li>
                        <h3 id="total_coins_purchase">{{ $data['total_coins_purchase'] }}</h3>
                        <p>Total golds purchased <span data-toggle="tooltip" title="Total number of golds purchased">?</a></span>
                    </li>
                    <li>
                        <h3 id="average_revenue">{{ $data['average_revenue'] }}</h3>
                        <p>Average revenue <span data-toggle="tooltip" title="Average revenue per unique user">?</span></p>
                    </li>
                    <li>
                        <h3 id="average_skeleton_keys_purchased">{{ $data['average_skeleton_keys_purchased'] }}</h3>
                        <p>Average skeleton keys purchased <span data-toggle="tooltip" title="Average number of skeleton keys purchased">?</span></p>
                    </li>
                    <li>
                        <h3 id="total_amount_skeleton_keys_purchased">{{ $data['total_amount_skeleton_keys_purchased'] }}</h3>
                        <p>Total amount skeleton keys paid <span data-toggle="tooltip" title="Total amount paid for skeleton keys">?</span></p>
                    </li>
                    <li>
                        <h3 id="total_revenue_google_fees">{{ $data['total_revenue_google_fees'] }}</h3>
                        <p>Total revenue google fees <span data-toggle="tooltip" title="Total revenue from the store (and total minus any fees/commissions to Google)">?</span></p>
                    </li>
                    <li>
                        <h3 id="total_revenue_apple_fees">{{ $data['total_revenue_apple_fees'] }}</h3>
                        <p>Total revenue apple fees <span data-toggle="tooltip" title="Total revenue from the store (and total minus any fees/commissions to Apple)">?</span></p>
                    </li>
                    <li>
                        <h3>Pending</h3>
                        <p>Total amount of gold earned <span data-toggle="tooltip" title="Total amount of gold earned">?</span></p>
                    </li>
                    <li>
                        <h3>Pending</h3>
                        <p>Average amount of gold earned <span data-toggle="tooltip" title="Average amount of gold earned">?</span></p>
                    </li>
                    <li>
                        <h3>Pending</h3>
                        <p>Percentage of earned <span data-toggle="tooltip" title="Percentage of earned vs purchased coins">?</span></p>
                    </li>
                    <li>
                        <h3>Pending</h3>
                        <p>Amount of revenue event paid coins <span data-toggle="tooltip" title="Amount of revenue for each event paid coins">?</span></p>
                    </li>
                </ul>
            </div>
            <!-- END STORE -->

            <!-- AVATAR -->
            <div class="total_usersdetlis">
                <ul>
                    <div class="titleaddatep">
                        <div class="titleboxleft">
                            <h4>Avatar Items</h4>
                        </div>
                        <div class="daterightbox">
                            <form method="post" id="avtarDaterangepickerForm">
                                @csrf
                                <img src="{{ asset('admin_assets/images/datepicker.png') }}">
                                <input type="text" name="avtar_date" value="" />
                            </form>
                            <!-- <a href="javascript:void(0)" id="refresh_user" data-action="refresh" data-date="{{ $data['user_start_date']->format('d M Y').' - '.$data['user_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a> -->
                        </div>
                        <div class="refreshbox">
                            <a href="javascript:void(0)" id="refresh_avtar" data-action="refresh" data-date="{{ $data['user_start_date']->format('d M Y').' - '.$data['user_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a>
                        </div>
                    </div>
                    <li>
                        <h3 id="average_avatar_items_purchased">{{ $data['average_avatar_items_purchased'] }}</h3>
                        <p>Average avatar items purchased <span data-toggle="tooltip" title="Average number of avatar items purchased">?</span></p>
                    </li>
                    <li>
                        <h3 id="total_paid_avatar">{{ round($data['total_paid_avatar']) }}</h3>
                        <p>Total paid avatar <span data-toggle="tooltip" title="Total amount paid for avatars items">?</span></p>
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
            <!-- END AVTAR -->

            <!-- EVENTS -->
            <div class="total_usersdetlis">
                <ul>
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
                        </div>
                        <div class="refreshbox">
                            <a href="javascript:void(0)" id="refresh_event" data-action="refresh" data-date="{{ $data['event_user_start_date']->format('d M Y').' - '.$data['event_user_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a>
                        </div>
                    </div>
                    <li>
                        <h3 id="user_event_city">{{ round($data['user_event_city']) }}</h3>
                        <p>User event city <span data-toggle="tooltip" title="Number of users signed up for each event by city">?</span></p>
                    </li>
                    <li>
                        <h3 id="user_event_country">{{ round($data['user_event_country']) }}</h3>
                        <p>User event country <span data-toggle="tooltip" title="Number of users signed up for each event by country">?</span></p>
                    </li>
                    <li>
                        <h3 id="amount_revenue_event_paid_coins">{{ $data['amount_revenue_event_paid_coins'] }}</h3>
                        <p>Amount revenue event paid golds <span data-toggle="tooltip" title="Amount of revenue for each event paid golds">?</span></p>
                    </li>
                </ul>
            </div>
            <!-- END EVENTS -->

            
        </div>
    </div>
    
</div>
@endsection

@section('scripts')
   
    <script type="text/javascript">
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip(); 

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
                    },
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
                    },
                    ranges: {
                       'Today': [moment(), moment()],
                       'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                       'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                       'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                       'This Month': [moment().startOf('month'), moment().endOf('month')],
                       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                });
            }

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
                        }
                    }
                });
            }
            /* END USER DTAE RANGE FILETR */

            /* AVTAR DTATE RANGE FILTER */
            avtarDaterangepicker();
            function avtarDaterangepicker(){
                $('input[name="avtar_date"]').daterangepicker({ 
                    maxDate: new Date(),
                    startDate: userStartDate,
                    endDate: userEndDate,
                    locale: {
                        format: 'DD MMM YYYY',
                    }
                });
            }

            avtarDateFilter('refresh_avtar');

            $('#refresh_avtar').click(function(){
                var value = 'refresh_avtar';
                avtarDateFilter(value);                
            });

            $('input[name="avtar_date"]').change(function(e) {
                e.preventDefault();
                var value = 'daterangepicker';
                avtarDateFilter(value);   
            });

            function avtarDateFilter(value){
                if (value == 'refresh_avtar') {
                    var data = {"avtar_date":$('#refresh_avtar').attr('data-date')};
                } else {
                    data = $('#avtarDaterangepickerForm').serialize();
                }
                $.ajax({
                    type: "GET",
                    url: '{{ route("admin.getAvtarDateFilter") }}',
                    data: data,
                    beforeSend: function() {
                        if (value == 'refresh_avtar') {
                            $('#refresh_avtar i').addClass('fa-spin');    
                            avtarDaterangepicker();
                        }
                        $('#refresh_avtar').parents('.total_usersdetlis').css('opacity','0.5');    
                    },
                    success: function(response)
                    {
                        $('#refresh_avtar i').removeClass('fa-spin');    
                        $('#refresh_avtar').parents('.total_usersdetlis').css('opacity','1');    
                        if (response.status == true) {
                            $.each(response.data,function(index , value){
                                $('#'+index).text(value);
                            });
                            $('#avatar_image ul').html('');
                            $.each(response.data.total_items_purchased,function(index , value){
                                $('#avatar_image ul').append(`<li><img src="`+value['image']+`"><h5>Total Used : `+value['total_use']+`</h5></li>`);
                            });
                        }
                    }
                });
            }
            /* END AVTAR DTATE RANGE FILTER */


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
