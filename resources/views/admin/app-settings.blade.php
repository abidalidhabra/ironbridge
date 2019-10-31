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
                <div class="form-group">
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
              </div>
              <button type="submit" class="btn btn-success">Save</button>
          </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.sponser-hunts.assets.sponser-hunts')

<script>
    $(document).on('submit', '#updateAppSettings', function(e) {
        e.preventDefault();
        if(validate()) {
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
                        toastr.warning('You are not authorized to access this page.');
                    }
                }
            });
        }
    });
</script>
@endsection