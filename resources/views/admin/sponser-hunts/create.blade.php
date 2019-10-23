@section('title','Ironbridge1779 | Sponser Hunt Creation')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="">               
            <div class="col-md-12 text-right">
                <a href="{{ route('admin.sponser-hunts.index') }}" class="btn back-btn">Back</a>
            </div>
            @if(auth()->user()->hasPermissionTo('Add Treasure Locations'))
            <div class="col-md-12">
                <div class="row">
                    <h3>Add Sponser Hunt</h3>
                </div>
            </div>
            @endif
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box" id="formContainer">
        <form method="POST" id="addSponserHunt" action="{{ route('admin.sponser-hunts.store') }}">
            @csrf
            <div class="modal-body padboxset">
                <div class="modalbodysetbox">
                    <div class="form-group @error('season_name') has-error @enderror">
                        <label class="control-label">Season Name:</label>
                        <input 
                        type="text" 
                        class="form-control" 
                        placeholder="Enter custom name" 
                        name="season_name" 
                        id="season_name"
                        alias-name="Season name"
                        minlength="5"
                        required>
                        @error('season_name')
                        <div class="text-muted text-danger"> {{ $errors->first('season_name') }} </div>
                        @enderror
                    </div>

                    <div class="form-group checkbox @error('active') has-error @enderror">
                        <label><input type="checkbox" name="active" id="active" value="true" checked>Active</label>
                        @error('active')
                        <div class="text-muted text-danger"> {{ $errors->first('active') }} </div>
                        @enderror
                    </div>
                    <div class="col-md-12" id="hunts-container">
                        <h4>Hunts</h4>
                        <div class="hunt-container">
                            @include('admin.sponser-hunts.partials.hunt-creation', ['index'=> 0])
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save</button>
                {{-- <button type="button" class="btn btn-danger btn-cancel">Cancel</button> --}}
                <input type="button" id="resetPolygon" value="Reset" style="display: none;" />
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.sponser-hunts.assets.sponser-hunts')

<script>
    $(document).on('submit', '#addSponserHunt', function(e) {
        e.preventDefault();
        if(validate()) {
            $.ajax({
                type: "POST",
                url: '{{ route('admin.sponser-hunts.store') }}',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response)
                {
                    if (response.status == true) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = '{{ route('admin.sponser-hunts.index') }}';
                        }, 2000)
                    } else {
                        toastr.warning('You are not authorized to access this page.');
                    }
                }
            });
        }
    });
</script>
@endsection