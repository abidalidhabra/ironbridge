@section('title','Ironbridge1779 | Sponser Hunt Creation')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="customdatatable_box" id="formContainer">
        <form method="POST" id="updateAppSettings" class="appstbboxcover" action="{{ route('admin.app.settings.update') }}">
            @csrf
            @method('PUT')
            <div class="appstbboxin">
                <h4>App Settings</h4>
               <!--  <div class="form-group">
                    <label class="control-label">Maintenance Mode:</label>
                    <label class="radio-inline">
                        <input 
                        type="radio" 
                        name="maintenance"
                        value="true" 
                        {{ ($settings->maintenance == true)? 'checked': '' }}>ON
                  </label>
                  <label class="radio-inline">
                        <input 
                        type="radio" 
                        name="maintenance"
                        value="false" 
                        {{ ($settings->maintenance == false)? 'checked': '' }}>OFF
                  </label>
                </div> -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Maintenance Time(UTC):</label>
                            <input type="text" name="maintenance_time" class="form-control datepicker" value="{{ ($settings->maintenance_time)?$settings->maintenance_time['start']->toDateTime()->format('d-m-Y h:i A').' - '.$settings->maintenance_time['end']->toDateTime()->format('d-m-Y h:i A'):'' }}" placeholder="Enter the start"  autocomplete="off" id="startdate">
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Maintenance End Time(UTC):</label>
                            <input type="text" name="end" class="form-control datepicker" value="{{ ($settings->maintenance_time)?$settings->maintenance_time['end']->toDateTime()->format('d-m-Y h:i A'):'' }}" placeholder="Enter the end" autocomplete="off" id="enddate">
                        </div>
                    </div> -->
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Android Version:</label>
                            <input type="text" name="android_version" class="form-control" value="{{ ($settings->app_versions)?$settings->app_versions['android']:'' }}" placeholder="Enter the android version">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Ios Version:</label>
                            <input type="text" name="ios_version" class="form-control" value="{{ $settings->app_versions['ios'] }}" placeholder="Enter the ios version">
                        </div>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Google Web API Key:</label>
                            <input type="text" name="google_keys[web]" class="form-control" value="{{ ($settings->google_keys)?base64_encode($settings->google_keys['web']):'' }}" placeholder="Enter the web key">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Google Android API Key:</label>
                            <input type="text" name="google_keys[android]" class="form-control" value="{{ ($settings->google_keys)?base64_encode($settings->google_keys['android']):'' }}" placeholder="Enter the android key">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Google IOS API Key:</label>
                            <input type="text" name="google_keys[ios]" class="form-control" value="{{ ($settings->google_keys)?base64_encode($settings->google_keys['ios']):'' }}" placeholder="Enter the ios key">
                        </div>
                    </div>
                </div> -->
                <div class="form-group">
                    <label class="control-label">Base Url:</label>
                    <input type="text" name="base_url" class="form-control" value="{{ $settings->base_url }}" placeholder="Enter the base url">
                </div>
              <button type="submit" class="btn btn-success">Save</button>
          </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')

<script>
    let startDate = "{{ (isset($settings->maintenance_time['start']))?$settings->maintenance_time['start']->toDateTime()->format('d-m-Y h:i A'): now()->format('D-M-Y h:i A') }}";
    let endDate = "{{ (isset($settings->maintenance_time['end']))?$settings->maintenance_time['end']->toDateTime()->format('d-m-Y h:i A'): now()->format('D-M-Y h:i A') }}";
    /*$('.datepicker').datetimepicker({
        format: 'DD-MM-YYYY hh:mm A'
    });*/
    console.log(moment().utc().format('MMMM Do YYYY, h:mm:ss a'));
    console.log(startDate);
    $('input[name="maintenance_time"]').daterangepicker({
        timePicker: true,
        // startDate: moment().startOf('hour'),
        // endDate: moment().startOf('hour').add(32, 'hour'),
        locale: {
          format: 'DD-MM-YYYY hh:mm A'
        },
        startDate: startDate,
        endDate: endDate,
      });

    /*$('#startdate').datetimepicker({
        minDate: new Date(),
        format: "DD-MM-YYYY hh:mm A",
        // defaultDate : new Date(),
    });
    $('#enddate').datetimepicker({
        minDate: new Date(),
        format: "DD-MM-YYYY hh:mm A",
    });

    $("#startdate").on("dp.change", function (e) {
        $('#enddate').data("DateTimePicker").setMinDate(e.date);
    });
    $("#enddate").on("dp.change", function (e) {
        $('#startdate').data("DateTimePicker").setMaxDate(e.date);
    });*/

    $(document).on('submit', '#updateAppSettings', function(e) {
        e.preventDefault();
        // if(validate()) {
            $.ajax({
                type: "POST",
                url: '{{ route('admin.app.settings.update') }}',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response)
                {
                    if (response.status == true) {
                        toastr.success(response.message);
                    } else {
                        toastr.warning(response.message);
                        // toastr.warning('You are not authorized to access this page.');
                    }
                }
            });
        // }
    });
</script>
@endsection