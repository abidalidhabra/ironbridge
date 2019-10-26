@section('title','Ironbridge1779 | Season Creation')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="">               
            <div class="col-md-12 text-right">
                <a href="{{ route('admin.seasons.index') }}" class="btn back-btn">Back</a>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <h3>Add Season</h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <form method="POST" id="addSeasonForm" action="{{ route('admin.seasons.store') }}">
            @csrf
            <div class="modal-body padboxset">
                <div class="modalbodysetbox">
                    <div class="addrehcover">
                        <div class="form-group @error('season_name') has-error @enderror">
                            <label class="control-label">Season Name:</label>
                            <input 
                            type="text" 
                            class="form-control" 
                            placeholder="Enter season name" 
                            name="season_name" 
                            alias-name="Season name"
                            minlength="5"
                            required>
                            @error('season_name')
                            <div class="text-muted text-danger"> {{ $errors->first('season_name') }} </div>
                            @enderror
                        </div>
                        
                        <div class="form-group @error('season_slug') has-error @enderror">
                            <label class="control-label">Season slug:</label>
                            <input 
                            type="text" 
                            class="form-control" 
                            placeholder="Enter season slug" 
                            name="season_slug" 
                            alias-name="Season Slug"
                            minlength="5"
                            required>
                            @error('season_slug')
                            <div class="text-muted text-danger"> {{ $errors->first('season_slug') }} </div>
                            @enderror
                        </div>

                        <div class="form-group @error('active_icon') has-error @enderror">
                            <label class="control-label">Active icon for season:</label>
                            <input 
                            type="file" 
                            class="form-control" 
                            name="active_icon" 
                            alias-name="Active icon for season"
                            required>
                            @error('active_icon')
                            <div class="text-muted text-danger"> {{ $errors->first('active_icon') }} </div>
                            @enderror
                        </div>

                        <div class="form-group @error('inactive_icon') has-error @enderror">
                            <label class="control-label">Inactive icon for season:</label>
                            <input 
                            type="file" 
                            class="form-control" 
                            name="inactive_icon" 
                            alias-name="Active icon for season"
                            required>
                            @error('inactive_icon')
                            <div class="text-muted text-danger"> {{ $errors->first('inactive_icon') }} </div>
                            @enderror
                        </div>

                        <div class="form-group checkbox @error('active') has-error @enderror">
                            <label><input type="checkbox" name="active" value="true" checked>Active</label>
                            @error('active')
                            <div class="text-muted text-danger"> {{ $errors->first('active') }} </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save</button>
                <button type="button" class="btn btn-danger" id="resetTheForm" onclick="document.getElementById('addSeasonForm').reset()">Reset</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')

<script>
    $(document).on('submit', '#addSeasonForm', function(e) {
        e.preventDefault();
        if(validate()) {
            $.ajax({
                type: "POST",
                url: '{{ route('admin.seasons.store') }}',
                data: new FormData(this),
                contentType: false,
                processData: false,
                cache: false,
                success: function(response)
                {
                    if (response.status == true) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = '{{ route('admin.seasons.index') }}';
                        }, 2000)
                    } else {
                        toastr.warning('You are not authorized to access this page.');
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