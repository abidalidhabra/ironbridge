@section('title','Ironbridge1779 | Add Events')
@extends('admin.layouts.admin-app')
@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
@endsection
@section('content')
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
        <form method="POST" id="addEventForm" enctype="multipart/form-data">
            @csrf
             <div class="daingaemtitlebox">
                <h4>Basic Details</h4>
            </div>
            <div class="allbasicdirmain">                
                <div class="allbasicdirbox">                
                   
                    <div class="form-group col-md-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Enter the name">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="centeric_points[0]" class="form-control" placeholder="Enter latitude">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="centeric_points[1]" class="form-control" placeholder="Enter longitude">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Initial Radius</label>
                        <input type="text" name="total_radius" class="form-control" id="total_radius" placeholder="Initial Radius">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Minimal Radius</label>
                        <input type="text" name="least_radius" class="form-control" id="least_radius" placeholder="Minimal Radius">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Maximum compasses values to be distribute</label>
                        <input type="text" name="weekly_max_compasses" class="form-control" id="weekly_max_compasses" placeholder="Maximum compasses values to be distribute">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Radius tobe deduce on each compass usage</label>
                        <input type="text" name="deductable_radius" class="form-control" id="deductable_radius" placeholder="Radius tobe deduce on each compass usage">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="text" name="start_time" class="form-control datetimepicker startDate" placeholder="Enter the start date">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="text" name="end_time" class="form-control datetimepicker endDate" placeholder="Enter the date">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">City</label>
                        <select name="city_id" class="form-control">
                            <option>Select city</option>
                            @forelse($cities as $key=>$city)
                            <option value="{{ $city->_id }}">{{ $city->name }}</option>
                            @empty
                            <option>Record Not found</option>
                            @endforelse
                        </select>
                    </div>
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
    <script type="text/javascript">

        /* DATE TIME PICKER */
        $('.startDate').datetimepicker({
            format: "MM/DD/YYYY hh:mm A",
            minDate: moment(),
        });

        $('.endDate').datetimepicker({
            format: "MM/DD/YYYY hh:mm A",
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

        /* SUBMIT FORM */
        $(document).on('submit','#addEventForm',function(e){
            e.preventDefault();
            formData = new FormData($(this)[0]);
            $.ajax({
                type:'POST',
                url:'{{ route("admin.events.store") }}',
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                beforeSend:function(){},
                success:function(response) {
                    toastr.success(response.message);
                    window.location.href = '{{ route("admin.events.index")}}';
                },
                complete:function(){},
                error:function(jqXHR, textStatus, errorThrown){
                    toastr.error(JSON.parse(jqXHR.responseText).message);
                }
            });
        })
    </script>
@endsection