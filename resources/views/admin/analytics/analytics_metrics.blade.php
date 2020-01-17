@section('title','Ironbridge1779 | Analytics')
@extends('admin.layouts.admin-app')
@section('styles')


@endsection('styles')

@section('content')

<div class="right_paddingboxpart">      
    <div class="centr_paretboxpart analytmetri_coverbox">
        <div class="signeup_topbox">
            <div class="signeup_lefttextbox">
                <h3>Analytics</h3>
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
                    <div class="hunt_game_box">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-9">
                                    <h4>Top 5 users with highest XP</h4>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('admin.analyticsMetrics.XPList') }}" class="btn btn-info btn-md view-btn">View More</a>
                                </div>
                            </div>
                            <div class="row" id="highest_xp_box">
                                @foreach($data['highest_xp'] as $key => $value)
                                <div class="col-md-8">
                                    <p class="text-left">
                                        <a href="{{ route('admin.accountInfo',$value['_id']) }}">
                                            {{ $value['first_name'].' '.$value['last_name'] }}
                                        </a>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-right">{{ number_format($value['agent_status']['xp']) }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-9">
                                    <h4>Top 5 users with highest relic</h4>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('admin.analyticsMetrics.relicsList') }}" class="btn btn-info btn-md view-btn">View More</a>
                                </div>
                            </div>
                            <div class="row" id="highest_relic_box">
                                @foreach($data['highest_relic'] as $key => $value)
                                <div class="col-md-8">
                                    <p class="text-left">
                                        <a href="{{ route('admin.accountInfo',$value['_id']) }}">
                                            {{ $value['first_name'].' '.$value['last_name'] }}
                                        </a>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-right">{{ number_format(count($value['relics'])) }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                </ul>
            </div>
            <!-- END USER -->

            <!-- Tutorials -->
            <div class="total_usersdetlis">
               <ul>
                <div class="titleaddatep">
                    <div class="titleboxleft">
                        <h4>Tutorials</h4>
                    </div>
                    <div class="daterightbox">
                        <form method="post" id="tutorialDaterangepickerForm">
                            @csrf
                            <img src="{{ asset('admin_assets/images/datepicker.png') }}">
                            <input type="text" name="tutorial_date" value="" />
                        </form>
                    </div>
                    <div class="refreshbox">
                        <a href="javascript:void(0)" id="refresh_tutorial" data-action="refresh" data-date="{{ $data['user_start_date']->format('d M Y').' - '.$data['user_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a>
                    </div>
                </div>
                <div class="hunt_game_box">
                    <div class="col-md-6">
                        <!-- <h4>Tutorials</h4> -->
                        <div class="row" id="tutorials_box1">
                            @foreach(array_slice($data['tutorials'],0,6) as $key => $value)
                            <div class="col-md-8">
                                <p class="text-left">{{ ucfirst(str_replace('_',' ',$key)) }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-right">{{ $value }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- <h4>Tutorials</h4> -->
                        <div class="row" id="tutorials_box2">
                            @foreach(array_slice($data['tutorials'],7,14) as $key => $value)
                            <div class="col-md-8">
                                <p class="text-left">{{ ucfirst(str_replace('_',' ',$key)) }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-right">{{ $value }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </ul>
        </div>
        <!-- End Tutorials -->

        <!-- HUNT -->
        <div class="total_usersdetlis">
            <ul>
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
                    <h3 id="played_random_hunts">{{ $data['played_random_hunts'] }}</h3>
                    <p>Played random hunts <span data-toggle="tooltip" title="Percentage of user played random hunts">?</span></p>
                </li>
                <li>
                    <h3 id="completed_random_hunts">{{ $data['completed_random_hunts'] }}</h3>
                    <p>Completed random hunts <span data-toggle="tooltip" title="Percentage of user completed random hunts">?</span></p>
                </li>
                <li>
                    <h3 id="played_relic_hunts">{{ $data['played_relic_hunts'] }}</h3>
                    <p>Played relic hunts <span data-toggle="tooltip" title="Percentage of user completed random hunts">?</span></p>
                </li>
                <li>
                    <h3 id="completed_relic_hunts">{{ $data['completed_relic_hunts'] }}</h3>
                    <p>Completed relic hunts <span data-toggle="tooltip" title="Percentage of user completed relic hunts">?</span></p>
                </li>
                <li>
                    <h3 id="average_of_random_hunts">{{ $data['average_of_random_hunts'] }}</h3>
                    <p>Average of Random hunt per player <span data-toggle="tooltip" title="Average of Random hunt per player">?</span></p>
                </li>
                <li>
                    <h3 id="average_of_relic_hunts">{{ $data['average_of_relic_hunts'] }}</h3>
                    <p>Average of Relic hunt per player <span data-toggle="tooltip" title="Average of Random hunt per player">?</span></p>
                </li>
            </ul>
        </div>
        <!-- END HUNT -->
    </div>
</div>
</div>

@endsection

@section('scripts')

<script type="text/javascript">
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip(); 

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

        $('input[name="user_date"]').change(function(e) {
            e.preventDefault();
            var value = 'daterangepicker';
            userDateFilter(value);   
        });

        $('#refresh_user').click(function(){
            var value = 'refresh_user';
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
                        $('#highest_xp_box').html('');
                        $.each(response.data.highest_xp,function(index , value){
                            $('#highest_xp_box').append(`<div class="col-md-8">
                                <p class="text-left"><a href="{{ route("admin.accountInfo","/") }}/`+value._id+`">`+value.first_name+' '+value.last_name+`</a></p>
                                </div><div class="col-md-4">
                                <p class="text-right">`+value.agent_status['xp']+`</p>
                                </div>`);
                        });

                        $('#highest_relic_box').html('');
                        $.each(response.data.highest_relic,function(index , value){
                            $('#highest_relic_box').append(`<div class="col-md-8">
                                <p class="text-left"><a href="{{ route("admin.accountInfo","/") }}/`+value._id+`">`+value.first_name+' '+value.last_name+`</a></p>
                                </div><div class="col-md-4">
                                <p class="text-right">`+value.relics.length+`</p>
                                </div>`);
                        });
                    }
                }
            });
        }
        /* END USER DTAE RANGE FILETR */

        /* TUTORIALS */
        var tutorialStartDate =  "{{ $data['user_start_date']->format('d M Y') }}";
        var tutorialEndDate = "{{ $data['user_end_date']->format('d M Y') }}";

        tutorialDaterangepicker();
        function tutorialDaterangepicker(){
            $('input[name="tutorial_date"]').daterangepicker({ 
                maxDate: new Date(),
                startDate: tutorialStartDate,
                endDate: tutorialEndDate,
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

        $('input[name="tutorial_date"]').change(function(e) {
            e.preventDefault();
            var value = 'daterangepicker';
            tutorialDateFilter(value);   
        });
        $('#refresh_tutorial').click(function(){
            var value = 'refresh_tutorial';
            tutorialDateFilter(value);                
        });

        function tutorialDateFilter(value){
            if (value == 'refresh_tutorial') {
                var data = {"tutorial_date":$('#refresh_tutorial').attr('data-date')};
            } else {
                data = $('#tutorialDaterangepickerForm').serialize();
            }
            $.ajax({
                type: "GET",
                url: '{{ route("admin.getTutorialDateFilter") }}',
                data: data,
                beforeSend: function() {
                    if (value == 'refresh_tutorial') {
                        $('#refresh_tutorial i').addClass('fa-spin');    
                        userDaterangepicker();
                    }
                    $('#refresh_tutorial').parents('.total_usersdetlis').css('opacity','0.5');    
                },
                success: function(response)
                {
                    $('#refresh_tutorial i').removeClass('fa-spin');    
                    $('#refresh_tutorial').parents('.total_usersdetlis').css('opacity','1');    
                    if (response.status == true) {
                        // $('#tutorials_box').html('');
                        // $.each(response.data.tutorials,function(index , value){
                        //     $('#tutorials_box').append(`<div class="col-md-8">
                        //         <p class="text-left">`+index+`</p>
                        //         </div><div class="col-md-4">
                        //         <p class="text-right">`+value+`</p>
                        //         </div>`);
                        // });
                        $('#tutorials_box1').html('');
                        $.each(response.data.tutorials1,function(index , value){
                            $('#tutorials_box1').append(`<div class="col-md-8">
                                <p class="text-left">`+index+`</p>
                                </div><div class="col-md-4">
                                <p class="text-right">`+value+`</p>
                                </div>`);
                        });
                        $('#tutorials_box2').html('');
                        $.each(response.data.tutorials2,function(index , value){
                            $('#tutorials_box2').append(`<div class="col-md-8">
                                <p class="text-left">`+index+`</p>
                                </div><div class="col-md-4">
                                <p class="text-right">`+value+`</p>
                                </div>`);
                        });
                    }
                }
            });
        }
        /* END TUTORIALS */


        /* START HUNT */
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
                    }
                }
            });
        }
        /* END START */
    });       
</script>
@endsection
