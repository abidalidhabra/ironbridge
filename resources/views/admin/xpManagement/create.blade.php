@section('title','Ironbridge1779 | XP Management')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="">               
            <div class="col-md-12 text-right">
                <a href="{{ route('admin.xpManagement.index') }}" class="btn back-btn">Back</a>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <h3>Add Hunts XP</h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <form method="POST" id="addXpManagementForm">
            @csrf
            <div class="modal-body padboxset">
                <div class="modalbodysetbox">
                    <div class="addrehcover">
                        <div class="form-group">
                            <label>Event:</label>
                            <select name="event" class="form-control">
                                <option value="">Select event</option>
                                <option value="clue_completion">Clue Completion</option>
                                <option value="treasure_completion">Treasure Completion</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>XP:</label>
                            <input type="number" name="xp" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.relics.scripts.relic-script')
<script>
    
    $(document).on('submit', '#addXpManagementForm', function(e) {

        e.preventDefault();
        if(validate()) {
            $.ajax({
                type: "POST",
                url: "{{ route('admin.xpManagement.store') }}",
                data: new FormData(this),
                contentType: false,
                processData: false,
                cache: false,
                beforeSend: function() {    
                    $('body').css('opacity','0.5');
                },
                success: function(response)
                {
                    $('body').css('opacity','1');
                    if (response.status == true) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = '{{ route('admin.xpManagement.index') }}';
                        }, 2000)
                    } else {
                        toastr.warning(response.message);
                        // toastr.warning('You are not authorized to access this page.');
                    }
                },
                error: function(xhr, exception) {
                    let error = JSON.parse(xhr.responseText);
                    toastr.error(error.message);
                }
            });
        }
    });
</script>
@endsection