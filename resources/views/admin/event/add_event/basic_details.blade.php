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
                <a href="{{ route('admin.event.index') }}" class="btn btn-info btn-md">Back</a>
            </div>
        </div>
    </div>
   
    <div class="customdatatable_box">
        <form method="POST" id="addEventForm" enctype="multipart/form-data">
            @csrf

            <!-- START BASIC DETAILS -->
            <div class="daingaemtitlebox">
                <h4>Basic Details</h4>
            </div>
            <div class="allbasicdirmain">                
                <div class="allbasicdirbox">                
                   
                    <div class="fdaboxallset">
                        <div class="form-group col-md-4">
                            <label class="form-label">Event Name</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Enter the name" value="@if(isset($event->name)){{$event->name}}@endif">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Event Type 
                                <small>(Single / Multi day(s))</small>
                                <a data-toggle="tooltip" title="Single: Single-day small event. Multi-Day(s): Multiple days base big event." data-placement="right">?</a>
                            </label>
                            <select name="type" class="form-control">
                                <option>Select type</option>
                                <option value="single" @if(isset($event->type) && $event->type=='single') {{ 'selected' }} @endif>Single</option>
                                <option value="multi" @if(isset($event->type) && $event->type=='multi') {{ 'selected' }} @endif>Multi</option>
                            </select>
                        </div>
                        <input type="hidden" name="event_id" value="@if(isset($event->id)){{ $event->id }} @endif">
                        <div class="form-group col-md-4">
                            <label class="form-label">Coin Type</label>
                            <select name="coin_type" class="form-control">
                                <option>Select coin type</option>
                                <option value="ar" @if(isset($event->coin_type) && $event->coin_type=='ar') {{ 'selected' }} @endif>AR</option>
                                <option value="physical" @if(isset($event->coin_type) && $event->coin_type=='physical') {{ 'selected' }} @endif>PHYSICAL</option>
                            </select>
                        </div>
                    </div>
                    <div class="fdaboxallset">
                        @if(isset($event->coin_type))
                            <div class="form-group col-md-4 coin_number_box @if(isset($event->coin_type) && $event->coin_type == 'ar'){{ 'hidden' }}@endif">
                                <label class="form-label">Coin Number
                                    <a data-toggle="tooltip" title="Physical coin number which needs to enter into a game for claim prize." data-placement="right">?</a>
                                </label>
                                <input type="text" name="coin_number" class="form-control" placeholder="Enter the coin number" value="@if(isset($event->coin_number)){{ $event->coin_number }}@endif">
                            </div>
                        @else
                            <div class="form-group col-md-4 coin_number_box hidden">
                                <label class="form-label">Coin Number
                                    <a data-toggle="tooltip" title="Physical coin number which needs to enter into a game for claim prize." data-placement="right">?</a>
                                </label>
                                <input type="text" name="coin_number" class="form-control" placeholder="Enter the coin number" value="@if(isset($event->coin_number)){{ $event->coin_number }}@endif">
                            </div>
                        @endif
                        <div class="form-group col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="text" name="event_start_date" class="form-control" placeholder="Enter the start date" value="@if(isset($event->starts_at)){{ $event->starts_at->format('d-m-Y') }} @endif" id="startdate" autocomplete="off">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="text" name="event_end_date" class="form-control" placeholder="Enter the date" value="@if(isset($event->ends_at)){{ $event->ends_at->format('d-m-Y') }}@endif" id="enddate" autocomplete="off">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">City</label>
                            <select name="city_id" id="city_id" class="form-control">
                                <option value="">Select city</option>
                                @forelse($cities as $key=>$city)
                                <option value="{{ $city->_id }}" @if(isset($event->city_id) && $event->city_id==$city->_id) {{ 'selected' }} @endif>{{ $city->name }}</option>
                                @empty
                                <option>Record Not found</option>
                                @endforelse
                            </select>
                        </div>
                    </div>

                    <div class="fdaboxallset">
                        <div class="form-group col-md-4">
                            <label class="form-label">Rejection Ratio
                                <small> In percentage (enter 0 if no rejection ratio)</small>
                                <a data-toggle="tooltip" title="% of people who will be going to eliminate daily basis" data-placement="right">?</a>
                            </label>
                            <input type="text" name="rejection_ratio" class="form-control" placeholder="Enter the rejection ratio" value="@if(isset($event->rejection_ratio)){{ $event->rejection_ratio }} @endif">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Winning Ratio
                                <small> In fix amount (enter 0 if no winning ratio)</small>
                                <a data-toggle="tooltip" title="# of people who will be going to win this game" data-placement="right">?</a>
                            </label>
                            <input type="text" name="winning_ratio" class="form-control" id="winning_ratio" placeholder="Enter the winning ratio" value="@if(isset($event->winning_ratio)){{ $event->winning_ratio }}@endif">
                        </div>
                    </div>

                    <div class="fdaboxallset">
                        <div class="form-group col-md-4">
                            <label class="form-label">Entry Fees(In gold)</label>
                            <input type="text" name="fees" class="form-control" placeholder="Enter the fees" value="@if(isset($event->fees)){{ $event->fees }}@endif">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Discount expire date</label>
                            <input type="text" name="discount_date" class="form-control" id="discount_date" placeholder="Enter the discount date" value="@if(isset($event->discount_till)){{ $event->discount_till->format('d-m-Y') }}@endif" autocomplete="off">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Discount %</label>
                            <input type="text" name="discount_fees" class="form-control" placeholder="Enter the discount fees" value="@if(isset($event->discount)){{ $event->discount }}@endif">
                        </div>
                    </div>


                    <div class="fdaboxallset">
                        <div class="form-group col-md-4">
                            <label class="form-label">Attempts
                                <a data-toggle="tooltip" title="# of attempts for doing best per game" data-placement="right">?</a>
                            </label>
                            <input type="text" name="attempts" class="form-control" placeholder="Enter the attempts" value="@if(isset($event->attempts)){{ $event->attempts }}@endif">
                        </div>
                        
                        <div class="form-group col-md-4">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" placeholder="Enter the description">@if(isset($event->description)){{ $event->description }}@endif</textarea>
                        </div>
                    </div>
                </div>
                 <div class="form-group Submitnextbtn">
                    <button type="submit" class="btn btn-success btnSubmit">SUBMIT & GO NEXT</button>
                </div>
            </div>
            <!-- END BASIC DETAILS -->

           
    </form>
    </div>
</div>


@endsection

@section('scripts')
     
    <script type="text/javascript">
        /* DATE TIME PICKER */
        //$('.datetimepicker').datetimepicker();
        $(function () {
            $('#city_id').select2();
            $('[name=type] , [name=coin_type]').select2({
              minimumResultsForSearch: Infinity
            });

            $('[data-toggle="tooltip"]').tooltip();   

            $('#discount_date').datepicker({
                startDate: new Date()
            });

           /* $('#startdate').datepicker({
                startDate: new Date()
            });

            $('#enddate').datepicker({
                startDate: new Date()
            });*/
            

            var startDate = new Date();
            $('#startdate').datepicker({
                weekStart: 1,
                startDate: startDate,
                autoclose: true,
            }).on('changeDate', function(selected){
                startDate = new Date(selected.date.valueOf());
                startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
                $('#enddate').datepicker('setStartDate', startDate);
            }); 

            $('#enddate').datepicker({
                weekStart: 1,
                startDate: startDate,
                autoclose: true
            }).on('changeDate', function(selected){
                FromEndDate = new Date(selected.date.valueOf());
                FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
                $('#startdate').datepicker('setEndDate', FromEndDate);
            });

            $(document).on('change','select[name="coin_type"]', function () {
                $('.coin_number_box').addClass('hidden');
                if($(this).val() == 'physical'){
                    $('.coin_number_box').removeClass('hidden')
                }
            });

        });
    


        /* SUBMIT FORM */
        $(document).on('submit','#addEventForm',function(e){
            e.preventDefault();
            formData = new FormData($(this)[0]);
            $.ajax({
                type:'POST',
                url:'{{ route("admin.event.addBasicStore") }}',
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                beforeSend:function(){},
                success:function(response) {
                    if (response.status == true) {
                        toastr.success(response.message);
                        window.location.href = '{{ route("admin.event.miniGame","/")}}/'+response.id;
                    } else {
                        toastr.warning(response.message);
                    }
                },
                complete:function(){},
                error:function(){}
            });
        })
    </script>
@endsection