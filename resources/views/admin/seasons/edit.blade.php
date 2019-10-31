@section('title','Ironbridge1779 | Season Edit')
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
                    <h3>Edit Season</h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <form method="POST" id="addSeasonForm" action="{{ route('admin.seasons.update', $season->id) }}">
            @csrf
            @method('PUT')
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
                            value="{{ $season->name }}" 
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
                            value="{{ $season->slug }}" 
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
                            alias-name="Active icon for season">
                            <b><a href="{{ asset('storage/seasons/'.$season->id.'/'.$season->active_icon) }}" target="_blank">VIEW</a></b>
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
                            alias-name="Active icon for season">
                            <b><a href="{{ asset('storage/seasons/'.$season->id.'/'.$season->inactive_icon) }}" target="_blank">VIEW</a></b>
                            @error('inactive_icon')
                            <div class="text-muted text-danger"> {{ $errors->first('inactive_icon') }} </div>
                            @enderror
                        </div>

                        <div class="form-group checkbox @error('active') has-error @enderror">
                            <label>
                                <input 
                                type="checkbox" 
                                name="active" 
                                value="true" {{ ($season->active)? 'checked': '' }}>Active
                            </label>
                            @error('active')
                            <div class="text-muted text-danger"> {{ $errors->first('active') }} </div>
                            @enderror
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
    $(document).on('submit', '#addSeasonForm', function(e) {
        e.preventDefault();
        if(validate()) {
            $.ajax({
                type: "POST",
                url: '{{ route('admin.seasons.update', $season->id) }}',
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