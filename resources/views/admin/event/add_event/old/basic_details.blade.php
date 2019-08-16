@section('title','Ironbridge1779 | GAME VARIATION')
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
    <br/><br/>
    <div class="customdatatable_box">
        <form method="POST" id="addEventForm" enctype="multipart/form-data">
            @csrf

            <!-- START BASIC DETAILS -->
            <div class="daingaemtitlebox">
                <h4>Basic Details</h4>
            </div>
            <div class="allbasicdirmain">                
                <div class="allbasicdirbox">                
                   
                    <div class="form-group col-md-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Enter the name" value="@if(isset($event->name)){{$event->name}}@endif">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-control">
                            <option>Select type</option>
                            <option value="single" @if(isset($event->type) && $event->type=='single') {{ 'selected' }} @endif>Single</option>
                            <option value="multi" @if(isset($event->type) && $event->type=='multi') {{ 'selected' }} @endif>Multi</option>
                        </select>
                    </div>
                    <input type="hidden" name="event_id" value="@if(isset($event->id)){{ $event->id }} @endif">
                    <div class="form-group col-md-3">
                        <label class="form-label">Coin Type</label>
                        <select name="coin_type" class="form-control">
                            <option>Select coin type</option>
                            <option value="ar" @if(isset($event->coin_type) && $event->coin_type=='ar') {{ 'selected' }} @endif>AR</option>
                            <option value="physical" @if(isset($event->coin_type) && $event->coin_type=='physical') {{ 'selected' }} @endif>PHYSICAL</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">City</label>
                        <select name="city_id" id="city_id" class="form-control">
                            <option>Select city</option>
                            @forelse($cities as $key=>$city)
                            <option value="{{ $city->_id }}" @if(isset($event->city_id) && $event->city_id==$key) {{ 'selected' }} @endif>{{ $city->name }}</option>
                            @empty
                            <option>Record Not found</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="text" name="event_start_date" class="form-control" placeholder="Enter the start date" value="@if(isset($event->starts_at)){{ $event->starts_at->format('d-m-Y h:i A') }} @endif"  data-date-format="DD-MM-YYYY hh:mm A" id="startdate" autocomplete="off">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="text" name="event_end_date" class="form-control" placeholder="Enter the date" value="@if(isset($event->ends_at)){{ $event->ends_at->format('d-m-Y h:i A') }}@endif"  data-date-format="DD-MM-YYYY hh:mm A" id="enddate" autocomplete="off">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Rejection Ratio<small> In percentage</small></label>
                        <input type="text" name="rejection_ratio" class="form-control" placeholder="Enter the rejection ratio" value="@if(isset($event->rejection_ratio)){{ $event->rejection_ratio }} @endif">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Winning Ratio<small> In fix amount</small></label>
                        <input type="text" name="winning_ratio" class="form-control" id="winning_ratio" placeholder="Enter the winning ratio" value="@if(isset($event->winning_ratio)){{ $event->winning_ratio }}@endif">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Fees & Gold</label>
                        <input type="text" name="fees" class="form-control" placeholder="Enter the fees" value="@if(isset($event->fees)){{ $event->fees }}@endif">
                    </div>
                </div>
            </div>
            <!-- END BASIC DETAILS -->

            <div class="form-group col-md-12">
                <button type="submit" class="btn btn-success btnSubmit">SUBMIT & GO NEXT</button>
            </div>
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

            $('#startdate,#enddate').datetimepicker({
                useCurrent: false,
                format: "DD-MM-YYYY hh:mm A",
                minDate: moment()
            });
            $('#startdate').datetimepicker().on('dp.change', function (e) {
                var incrementDay = moment(new Date(e.date));
                incrementDay.add(1, 'days');
                $('#enddate').data('DateTimePicker').setMinDate(incrementDay);
                $(this).data("DateTimePicker").hide();
            });

            $('#enddate').datetimepicker().on('dp.change', function (e) {
                var decrementDay = moment(new Date(e.date));
                decrementDay.subtract(1, 'days');
                $('#startdate').data('DateTimePicker').setMaxDate(decrementDay);
                 $(this).data("DateTimePicker").hide();
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