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
                    <h3>Edit XP Management</h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <form method="POST" id="editXpManagemant">
            @csrf
            @method('PUT')
            <div class="modal-body padboxset">
                <div class="modalbodysetbox">
                    <div class="addrehcover">
                       <div class="form-group">
                            <label>Event:</label>
                            <select name="event" class="form-control">
                                <option value="">Select event</option>
                                <option value="clue_completion" {{ ($xpManagement->event=='clue_completion')?'selected':'' }}>Clue Completion</option>
                                <option value="treasure_completion" {{ ($xpManagement->event=='treasure_completion')?'selected':'' }}>Treasure Completion</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>XP:</label>
                            <input type="number" value="{{ $xpManagement->xp }}" name="xp" class="form-control">
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
<script>
    $(document).on('submit', '#editXpManagemant', function(e) {
        e.preventDefault();
            $.ajax({
                type: "POST",
                url: '{{ route('admin.xpManagement.update', $xpManagement->id) }}',
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
                    }
                },
            });
    });

</script>
@endsection