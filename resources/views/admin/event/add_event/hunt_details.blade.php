@section('title','Ironbridge1779 | Events')
@extends('admin.layouts.admin-app')
@section('styles')
    

@endsection
@section('content')
<style type="text/css">
    #map {
        height: 400px;
        width: 100%;
      }
</style>
<div class="right_paddingboxpart">      
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Add Event</h3>
            </div>
            <div class="col-md-6 text-right modalbuttonadd">
                <a href="{{ route('admin.event.basicDetails',$id) }}" class="btn btn-info btn-md">Back</a>                
            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <form method="POST" id="addEventForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="">
            <!-- HUNT CLUE START CODE -->
            <div class="daingaemtitlebox">
                <h4>Hunt Details</h4>
            </div>
            <div class="allbasicdirmain">                
                <div class="allbasicdirbox">
                    <input type="hidden" name="event_id" value="{{ $id }}"> 
                    <div class="form-group col-md-4">
                        <label class="form-label">Map Reveal Date
                            <a data-toggle="tooltip" title="Hunt completion date and time for that event" data-placement="right">?</a>
                        </label>
                        <input type="text" name="map_reveal_date" class="form-control" id="map_reveal_date" placeholder="Enter the map reveal date" autocomplete="off" value="@if(isset($event->map_reveal_date)){{ $event->map_reveal_date->format('d-m-Y h:i A') }}@else {{ $event->ends_at->format('d-m-Y h:i A') }}@endif">
                    </div>
                    <div class="form-group col-md-5">
                        <label class="form-label">Search Place Name
                            <a data-toggle="tooltip" title="Hunt location for that event" data-placement="right">?</a>
                        </label>
                       
                        <select class="form-control hunts" name="search_place_name" id="hunts">
                            <option value="">Select Place</option>
                            @forelse($hunts as $hunt)
                                <option value="{{ $hunt->id }}" @if($event->hunt_id == $hunt->id){{ 'selected' }} @endif>{{ $hunt->name }}</option>
                            @empty
                            @endforelse
                        </select>
                        <!-- <input type="text" name="search_place_name" id="search_place_name" class="form-control" placeholder="Enter the Search Place Name" autocomplete="off"> -->
                    </div>
                    <div class="form-group col-md-3">
                        <div class="addhunteyrefbtn">
                            <a href="javascript:void(0)" class="btn hunt_details" data-toggle="tooltip" title="View Hunt"><i class="fa fa-eye "></i></a>
                            <a href="javascript:void(0)" class="btn" id="refresh" data-toggle="tooltip" title="Refresh Hunts List"><i class="fa fa-refresh"></i></a>
                            <a href="{{ route('admin.add_location') }}" target='_blank' class="btn" >Add New</a>
                        </div>
                    </div>
                </div>
                
            </div>
            <!-- END HUNT CLUE  -->

            <!-- PRIZE START CODE -->
            <div class="daingaemtitlebox">
                <h4>Prizes</h4>
            </div>
            <div class="allbasicdirmain">                
                <div class="allbasicdirbox">
                    <div class="">
                        <div class="form-group col-md-2">
                            <label class="form-label">Group Type
                                <a data-toggle="tooltip" title="Individual Prize or Bulk prize" data-placement="right">?</a>
                            </label>
                        </div>
                        
                        <div class="form-group col-md-3">
                            <label>Rank</label>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Prize</label>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Prize type</label>
                        </div>
                        <!-- <div class="form-group col-md-2">
                            <label>Map time delay</label>
                        </div> -->
                    </div>
                    <div id="prize_box">
                        @forelse($event->prizes as $key => $value)
                            <div class="prize_box">
                                <div class="form-group col-md-2">
                                    <select class="form-control group_type" name="group_type[{{ $key }}]">
                                        <option value="individual" @if(isset($value->group_type) && $value->group_type == "individual") {{ 'selected' }} @endif>Individual</option>
                                        <option value="group" @if(isset($value->group_type) && $value->group_type == "group") {{ 'selected' }} @endif>Group</option>
                                    </select>
                                </div>
                                <input type="hidden" name="prize_index" value="{{ $key }}">
                                <div class="form-group col-md-3 rank_box">
                                    @if(isset($value->group_type) && $value->group_type == "individual")
                                        <input type="text" name="rank[{{ $key }}]" class="form-control" placeholder="Rank" value="{{ $value->rank }}">
                                    @else
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" name="start_rank[{{ $key }}]" class="form-control col-md-12" placeholder="Start" value="{{ $value->start_rank }}">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="end_rank[{{ $key }}]" class="form-control col-md-12" placeholder="End" value="{{ $value->end_rank }}">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="text" name="prize[{{ $key }}]" class="form-control" placeholder="Prize" value="{{ $value->prize_value }}">
                                </div>
                                <div class="form-group col-md-2">
                                    <select class="form-control prize_type" name="prize_type[{{ $key }}]">
                                        <option value="cash" @if(isset($value->prize_type) && $value->prize_type == "cash") {{ 'selected' }} @endif>Cash</option>
                                        <option value="gold" @if(isset($value->prize_type) && $value->prize_type == "gold") {{ 'selected' }} @endif>Gold</option>
                                    </select>
                                </div>
                                <!-- <div class="form-group col-md-2">
                                    <input type="text" name="map_time_delay[{{ $key }}]" class="form-control" placeholder="Map time delay" value="{{ $value->map_time_delay }}">
                                </div> -->
                                <div class="form-group col-md-2 button_box">
                                    <?php
                                        $totalPrize = count($event->prizes)-1;
                                    ?>
                                    @if($totalPrize == $key)
                                        <a href="javascript:void(0)" class="btn add_prize"><i class="fa fa-plus "></i></a>
                                        @if($totalPrize != 0)
                                            <a href="javascript:void(0)" class="btn remove_prize"><i class="fa fa-minus "></i></a>
                                        @endif
                                    @else
                                        <a href="javascript:void(0)" class="btn remove_prize"><i class="fa fa-minus "></i></a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="prize_box">
                                <div class="form-group col-md-2">
                                    <select class="form-control group_type" name="group_type[]">
                                        <option value="individual">Individual</option>
                                        <option value="group">Group</option>
                                    </select>
                                </div>
                                <input type="hidden" name="prize_index" value="0">
                                <div class="form-group col-md-3 rank_box">
                                    <input type="text" name="rank[]" class="form-control" placeholder="Rank" data-action='rank'>
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="text" name="prize[]" class="form-control" placeholder="Prize">
                                </div>
                                <div class="form-group col-md-2">
                                    <select class="form-control prize_type" name="prize_type[]">
                                        <option value="cash">Cash</option>
                                        <option value="gold">Gold</option>
                                    </select>
                                </div>
                                <!-- <div class="form-group col-md-2">
                                    <input type="text" name="map_time_delay[]" class="form-control" placeholder="Map time delay">
                                </div> -->
                                <div class="form-group col-md-2 button_box">
                                    <a href="javascript:void(0)" class="btn add_prize"><i class="fa fa-plus "></i></a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <!-- END PRIZE START CODE -->
           
            <!-- MAP TIME DEALY --> 
            <div class="daingaemtitlebox">
                <h4>Map time delay</h4>
            </div>
            <div class="allbasicdirmain">                
                <div class="allbasicdirbox">
                    <div class="">
                        <div class="form-group col-md-3">
                            <label class="form-label">Group Type
                                <a data-toggle="tooltip" title="Individual Prize or Bulk prize" data-placement="right">?</a>
                            </label>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Rank</label>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Map time delay</label>
                        </div>
                    </div>
                    <div id="map_time_dealy_box">
                        @forelse($event->event_map_time_delay as $key => $value)
                            <div class="map_time_dealy_box">
                                <div class="form-group col-md-3">
                                    <select class="form-control map_time_group_type" name="map_time_group_type[{{ $key }}]">
                                        <option value="individual" @if(isset($value->group_type) && $value->group_type == "individual") {{ 'selected' }} @endif>Individual</option>
                                        <option value="group" @if(isset($value->group_type) && $value->group_type == "group") {{ 'selected' }} @endif>Group</option>
                                    </select>
                                </div>
                                <input type="hidden" name="map_time_index" value="{{ $key }}">
                                <div class="form-group col-md-3 rank_box">
                                    @if(isset($value->group_type) && $value->group_type == "individual")
                                        <input type="text" name="map_time_rank[{{ $key }}]" class="form-control" placeholder="Rank" value="{{ $value->rank }}">
                                    @else
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" name="map_time_start_rank[{{ $key }}]" class="form-control col-md-12" placeholder="Start" value="{{ $value->start_rank }}">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="map_time_end_rank[{{ $key }}]" class="form-control col-md-12" placeholder="End" value="{{ $value->end_rank }}">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-3">
                                    <input type="text" name="map_time_delay[{{ $key }}]" class="form-control" placeholder="Map time delay" value="{{ $value->map_time_delay/60 }}">
                                </div>
                                <div class="form-group col-md-2 button_box">
                                    <?php
                                        $totalPrize = count($event->event_map_time_delay)-1;
                                    ?>
                                    @if($totalPrize == $key)
                                        <a href="javascript:void(0)" class="btn add_map_time"><i class="fa fa-plus "></i></a>
                                        @if($totalPrize != 0)
                                            <a href="javascript:void(0)" class="btn remove_map_time"><i class="fa fa-minus "></i></a>
                                        @endif
                                    @else
                                        <a href="javascript:void(0)" class="btn remove_map_time"><i class="fa fa-minus "></i></a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="map_time_dealy_box">
                                <div class="form-group col-md-3">
                                    <select class="form-control map_time_group_type" name="map_time_group_type[]">
                                        <option value="individual">Individual</option>
                                        <option value="group">Group</option>
                                    </select>
                                </div>
                                <input type="hidden" name="map_time_index" value="0">
                                <div class="form-group col-md-3 rank_box">
                                    <input type="text" name="map_time_rank[]" class="form-control" placeholder="Rank">
                                </div>
                                <div class="form-group col-md-3">
                                    <input type="text" name="map_time_delay[]" class="form-control" placeholder="Map time delay">
                                </div>
                                <div class="form-group col-md-2 button_box">
                                    <a href="javascript:void(0)" class="btn add_map_time"><i class="fa fa-plus "></i></a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="form-group Submitnextbtn">
                    <!-- <a href="{{ url('admin/miniGame',$id) }}" class="btn btn-success btnSubmit">PREVIOUS</a> -->
                    <a href="{{ route('admin.event.miniGame',$id) }}" class="btn btn-success btnSubmit">PREVIOUS</a>
                    <button type="submit" class="btn btn-success btnSubmit">SUBMIT</button>
                </div>
            </div>
            <!-- END MAP TIME DEALY --> 
    </form>
    </div>
</div>


@endsection

@section('scripts')
    <script type="text/javascript">
        /* DATE TIME PICKER */
        $('[data-toggle="tooltip"]').tooltip();   
        $('.prize_type').select2({
          minimumResultsForSearch: Infinity
        });
        var startdate = '{{ $event->starts_at }}';
        var enddate = '{{ $event->ends_at }}';
        
        
        $('#map_reveal_date').datetimepicker({
            useCurrent: false,
            format: "DD-MM-YYYY hh:mm A",
            minDate: moment(enddate),
            // maxDate: moment(),
            // defaultDate: moment(startdate),
        });

        /* SUBMIT FORM */
        $(document).ready(function() {
     
            $(document).on('submit','#addEventForm',function(e){
                rankCheck();
                e.preventDefault();
                formData = new FormData($(this)[0]);
                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.event.addHuntDetails") }}',
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){},
                    success:function(response) {
                        if (response.status == true) {
                            toastr.success(response.message);
                            window.location.href = '{{ route("admin.event.index")}}';
                        } else {
                            toastr.warning(response.message);
                        }
                    },
                    complete:function(){},
                    error:function(){}
                });
            })

            $('.hunts').select2();

            $(document).on('change','select[name="search_place_name"]',function(){
                var hunt_id = $(this).val();
                var url = "{{ route('admin.boundary_map','/') }}/"+hunt_id;
                $('.hunt_details').attr('href',url).attr('target','_blank');
            });

            $(document).on('click','.hunt_details',function(){
                var url = $(this).attr('href');
                if (url == 'javascript:void(0)') {
                    toastr.warning('Please select the search place name');   
                }
            });

            /* REFRESH BUTTON */
            $(document).on('click','#refresh',function(){
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("admin.event.getHuntList") }}',
                    data: {id : '{{ $event->city_id }}'},
                    beforeSend: function(){
                        $('#refresh i').addClass('fa-spin');
                    },
                    success: function(response)
                    {
                        $('#refresh i').removeClass('fa-spin');
                        if (response.status == true) {
                            $('select[name="search_place_name"]').html('');
                            jQuery.each( response.data, function( index, val ) {
                                
                                $('select[name="search_place_name"]').append('<option value="'+val._id+'">'+val.name+'</option>')
                            });
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            });


            /* APPEND PRIZES */
            $(document).on('click','.add_prize',function(){
                $(this).parents('.button_box').find('.remove_prize').remove();
                $(this).parents('.button_box').append('<a href="javascript:void(0)" class="btn remove_prize"><i class="fa fa-minus "></i></a>');
                $(this).remove();
                let prizeIndex = $('input[name="prize_index"]:last').val();
                let currentIndex = parseInt(prizeIndex)+1;

                $('#prize_box').append(`<div class="prize_box">
                            <div class="form-group col-md-2">
                                <select class="form-control group_type" name="group_type[`+currentIndex+`]">
                                    <option value="individual">Individual</option>
                                    <option value="group">Group</option>
                                </select>
                            </div>
                            <input type="hidden" name="prize_index" value="`+currentIndex+`">
                            <div class="form-group col-md-2 rank_box">
                                <input type="text" name="rank[`+currentIndex+`]" class="form-control" placeholder="Rank" data-action='rank'>
                            </div>
                            <div class="form-group col-md-3">
                                <input type="text" name="prize[`+currentIndex+`]" class="form-control" placeholder="Prize">
                            </div>
                            <div class="form-group col-md-2">
                                <select class="form-control prize_type" name="prize_type[`+currentIndex+`]">
                                    <option value="cash">Cash</option>
                                    <option value="gold">Gold</option>
                                </select>
                            </div>
                            <!--<div class="form-group col-md-2">
                                <input type="text" name="map_time_delay[`+currentIndex+`]" class="form-control" placeholder="Map time delay">
                            </div>-->
                            <div class="form-group col-md-2 button_box">
                                <a href="javascript:void(0)" class="btn add_prize"><i class="fa fa-plus "></i></a>
                                <a href="javascript:void(0)" class="btn remove_prize"><i class="fa fa-minus "></i></a>
                            </div>
                        </div>`);
            });


            $(document).on('click','.remove_prize',function(){
                $(this).parents('.prize_box').remove();
                $('.add_prize').remove();
                $('#prize_box .prize_box:last').find('.button_box').prepend(`<a href="javascript:void(0)" class="btn add_prize"><i class="fa fa-plus "></i></a>`);
                if($('#prize_box .prize_box').length == 1){
                    $('.remove_prize').remove();
                }
            });

            $(document).on('change','.group_type',function(){
                var group_type = $(this).val();
                let prizeIndex = $(this).parents('.prize_box').find('input[name="prize_index"]').val();
                if (group_type == 'group') {
                    $(this).parents('.prize_box').find('.rank_box').html(`<div class="row">
                                                                    <div class="col-md-6">
                                                                        <input type="text" name="start_rank[`+prizeIndex+`]" class="form-control col-md-12 start_rank" placeholder="Start">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" name="end_rank[`+prizeIndex+`]" class="form-control col-md-12" placeholder="End">
                                                                    </div>
                                                                </div>`);
                } else if(group_type == 'individual'){

                    $(this).parents('.prize_box').find('.rank_box').html(`<input type="text" name="rank[`+prizeIndex+`]" class="form-control" placeholder="Rank">`);
                }
            });

        });

        /* Map time delay */
            $(document).on('click','.add_map_time',function(){
                $(this).parents('.button_box').find('.remove_map_time').remove();
                $(this).parents('.button_box').append('<a href="javascript:void(0)" class="btn remove_map_time"><i class="fa fa-minus "></i></a>');
                $(this).remove();
                let prizeIndex = $('input[name="map_time_index"]:last').val();
                let currentIndex = parseInt(prizeIndex)+1;

                $('#map_time_dealy_box').append(`<div class="map_time_dealy_box">
                            <div class="form-group col-md-3">
                                    <select class="form-control map_time_group_type" name="map_time_group_type[]">
                                        <option value="individual">Individual</option>
                                        <option value="group">Group</option>
                                    </select>
                                </div>
                            <input type="hidden" name="map_time_index" value="0">
                            <div class="form-group col-md-3 rank_box">
                                <input type="text" name="map_time_rank[]" class="form-control" placeholder="Rank">
                            </div>
                            <div class="form-group col-md-3">
                                <input type="text" name="map_time_delay[]" class="form-control" placeholder="Map time delay">
                            </div>
                            <div class="form-group col-md-2 button_box">
                                <a href="javascript:void(0)" class="btn add_map_time"><i class="fa fa-plus "></i></a>
                                <a href="javascript:void(0)" class="btn remove_map_time"><i class="fa fa-minus "></i></a>
                            </div>
                        </div>`);
            });

            $(document).on('click','.remove_map_time',function(){
                $(this).parents('.map_time_dealy_box').remove();
                $('.add_map_time').remove();
                $('#map_time_dealy_box .map_time_dealy_box:last').find('.button_box').prepend(`<a href="javascript:void(0)" class="btn add_map_time"><i class="fa fa-plus "></i></a>`);
                if($('#map_time_dealy_box .map_time_dealy_box').length == 1){
                    $('.remove_map_time').remove();
                }
            });

            /* MAP TIME DELAY RANK CLICK */
            $(document).on('focusout','input[name^="map_time_rank"] , input[name^="map_time_start_rank"] , input[name^="map_time_end_rank"]',function(){
                var rank_array = [];
                $('input[name^="rank"] , input[name^="start_rank"] , input[name^="end_rank"]').each(function() {
                    rank_array.push($(this).val());
                });
                var rank = parseInt($(this).val());
                var max_rank = Math.max.apply(Math,rank_array);
                var min_rank = Math.min.apply(Math,rank_array);

                if (min_rank > rank) {
                    toastr.warning('Min value rank '+ min_rank);
                } 

                if(max_rank < rank) {
                    toastr.warning('Max value rank '+ max_rank);
                }
            });

            function rankCheck(){
                $('input[name^="map_time_rank"] , input[name^="map_time_start_rank"] , input[name^="map_time_end_rank"]').each(function(){
                    var rank_array = [];
                    $('input[name^="rank"] , input[name^="start_rank"] , input[name^="end_rank"]').each(function() {
                        rank_array.push($(this).val());
                    });
                    var rank = parseInt($(this).val());
                    var max_rank = Math.max.apply(Math,rank_array);
                    var min_rank = Math.min.apply(Math,rank_array);

                    if (min_rank > rank) {
                        toastr.warning('Min value rank '+ min_rank);
                        return false;
                    } 

                    if(max_rank < rank) {
                        toastr.warning('Max value rank '+ max_rank);
                        return false;
                    }
                    
                    // return true;            
                });
            }

            $(document).on('change','.map_time_group_type',function(){
                var group_type = $(this).val();
                let mapIndex = $(this).parents('.map_time_dealy_box').find('input[name="map_time_index"]').val();
                if (group_type == 'group') {
                    $(this).parents('.map_time_dealy_box').find('.rank_box').html(`<div class="row">
                                                                    <div class="col-md-6">
                                                                        <input type="text" name="map_time_start_rank[`+mapIndex+`]" class="form-control col-md-12 start_rank" placeholder="Start">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" name="map_time_end_rank[`+mapIndex+`]" class="form-control col-md-12" placeholder="End">
                                                                    </div>
                                                                </div>`);
                } else if(group_type == 'individual'){

                    $(this).parents('.map_time_dealy_box').find('.rank_box').html(`<input type="text" name="map_time_rank[`+mapIndex+`]" class="form-control" placeholder="Rank">`);
                }
            });
        /* END MAP TIME DELAY */
    </script>
@endsection