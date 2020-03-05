@section('title','Ironbridge1779 | Events')
@extends('admin.layouts.admin-app')
@section('styles')
@endsection
@section('content')
<!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script> -->
<link rel="stylesheet" href="{{ asset('admin_assets/css/jClocksGMT.css') }}">
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Add Event</h3>
            </div>
            <div class="col-md-6 text-right modalbuttonadd">
                <a href="{{ route('admin.events.index') }}" class="btn btn-info btn-md">Back</a>
            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <form method="PUT" id="editEventForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
             <div class="daingaemtitlebox">
                <h4>Basic Details</h4>
            </div>
            <div class="allbasicdirmain">                
                <div class="allbasicdirbox">                
                   
                    <div class="form-group col-md-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ $event->name }}" placeholder="Enter the name">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="centeric_points[0]" class="form-control" value="{{ $event->centeric_points['lat'] }}" placeholder="Enter latitude">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="centeric_points[1]" class="form-control" value="{{ $event->centeric_points['lng'] }}" placeholder="Enter longitude">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Initial Radius</label>
                        <input type="text" name="total_radius" class="form-control" id="total_radius" value="{{ $event->total_radius }}" placeholder="Initial Radius">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Minimal Radius</label>
                        <input type="text" name="least_radius" class="form-control" id="least_radius" value="{{ $event->least_radius }}" placeholder="Minimal Radius">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Total Compasses</label>
                        <input type="text" name="total_compasses" class="form-control" id="total_compasses" value="{{ $event->total_compasses }}" placeholder="Total compasses">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Maximum compasses values allow to earn per week</label>
                        <input type="text" name="weekly_max_compasses" class="form-control" id="weekly_max_compasses" value="{{ $event->weekly_max_compasses }}" placeholder="Maximum compasses values to be distribute">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Radius tobe deduce on each compass usage</label>
                        <input type="text" name="deductable_radius" class="form-control" id="deductable_radius" value="{{ $event->deductable_radius }}" placeholder="Radius tobe deduce on each compass usage">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">City</label>
                        <select name="city_id" class="form-control">
                            <option>Select city</option>
                            @forelse($cities as $key=>$city)
                                <option value="{{ $city->_id }}" @if($city->id == $event->city_id) {{'selected'}} @endif>{{ $city->name }}</option>
                            @empty
                            <option>Record Not found</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="text" name="start_time" class="form-control datetimepicker startDate" placeholder="Enter the start date" readonly="">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="text" name="end_time" class="form-control datetimepicker endDate" placeholder="Enter the date" readonly="">
                    </div>
                    <!-- <div id="clock_hou"></div> -->
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-success btnSubmit">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


@endsection

@section('scripts')
<!-- <script type="text/javascript" src="https://momentjs.com/downloads/moment.js"></script> -->
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.28/moment-timezone.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.23/moment-timezone-with-data-2012-2022.js"></script>
<script src="{{ asset('admin_assets/js/jClocksGMT.js') }}"></script>
<script src="{{ asset('admin_assets/js/jquery.rotate.js') }}"></script>
    <script type="text/javascript">

        $('#city_id').select2();
        $('[data-toggle="tooltip"]').tooltip();

        /* SUBMIT FORM */
        $(document).on('submit','#editEventForm',function(e){
            e.preventDefault();
            let eventId = "{{ $event->id }}";
            formData = new FormData($(this)[0]);
            let route = '{{ route("admin.events.update",":id") }}';
            route = route.replace(':id', eventId);
            $.ajax({
                type:'POST',
                url: route,
                data: formData,
                // cache:false,
                contentType: false,
                processData: false,
                beforeSend:function(){},
                success:function(response) {
                    toastr.success(response.message);
                },
                complete:function(){},
                error:function(jqXHR, textStatus, errorThrown){
                    toastr.error(JSON.parse(jqXHR.responseText).message);
                }
            });
        });

         var startdate = '{{ $event->time["start"] }}';
         var enddate = '{{ $event->time["end"] }}';
        console.log(startdate, enddate);
         $('.startDate').datetimepicker({
            format: "MM/DD/YYYY hh:mm A",
            minDate: moment(),
            defaultDate: moment(startdate),
            // autoclose: true,
        });

         $('.endDate').datetimepicker({
            format: "MM/DD/YYYY hh:mm A",
            defaultDate: moment(enddate),
            // autoclose: true,
        });

         $('.startDate').datetimepicker().on('dp.change', function (e) {
            var incrementDay = moment(new Date(e.date));
            incrementDay.add(1, 'days');
            $('.endDate').data('DateTimePicker').setMinDate(incrementDay);
            $(this).data("DateTimePicker").hide();
        });

         $('.endDate').datetimepicker().on('dp.change', function (e) {
            var decrementDay = moment(new Date(e.date));
            decrementDay.subtract(1, 'days');
            $('.startDate').data('DateTimePicker').setMaxDate(decrementDay);
            $(this).data("DateTimePicker").hide();
        });

        // let abc = $('#clock_hou').jClocksGMT({
        //     title: 'Houston, TX, USA', 
        //     location: 'Asia/Kolkata',
        //     imgpath: '/public/admin_assets/'
        // });
        // console.log(abc);
    </script>
@endsection