@section('title','Ironbridge1779 | Notifications')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="customdatatable_box" id="formContainer">
        <form method="POST" id="addFormNotifation">
            @csrf
            <div class="appstbboxin">
                <h4>Notifications</h4>
               <div class="form-group">
                    <label class="control-label">Message:</label>
                    <textarea class="form-control" name="message" placeholder="Enter the notifications" rows="5"></textarea>
                </div>
              <button type="submit" class="btn btn-success">Save</button>
          </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')

<script>
    $(document).on('submit', '#addFormNotifation', function(e) {
        e.preventDefault();
        // if(validate()) {
            $.ajax({
                type: "POST",
                url: '{{ route('admin.notifications.store') }}',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response)
                {
                    if (response.status == true) {
                        toastr.success(response.message);
                        $('#addFormNotifation textarea').val('');
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