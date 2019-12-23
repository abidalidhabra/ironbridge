@section('title','Ironbridge1779 | Relic Creation')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="">               
            <div class="col-md-12 text-right">
                <a href="{{ route('admin.relics.index') }}" class="btn back-btn">Back</a>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <h3>Add Relic</h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <form method="POST" id="addRelicForm" action="{{ route('admin.seasons.store') }}">
            @csrf
            <div class="modal-body padboxset">
                <div class="modalbodysetbox">
                    <div class="addrehcover">
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter the name">
                        </div>
                        <div class="form-group @error('icon') has-error @enderror">
                            <label class="control-label">Image for relic:</label>
                            <input 
                            type="file" 
                            class="form-control" 
                            name="icon" 
                            alias-name="Icon for relic">
                            @error('icon')
                            <div class="text-muted text-danger"> {{ $errors->first('icon') }} </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>TH Complexity:</label>
                            <select name="complexity" class="form-control" alias-name="TH Complexity">
                                <option value="">Select TH Complexity</option>
                                @for($i = 1; $i<=5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Relic Map Pieces:</label>
                            <input type="number" name="pieces" class="form-control" placeholder="Enter the relic map pieces">
                        </div>
                        <div class="form-group">
                            <label>Number:</label>
                            <input type="number" name="number" class="form-control" placeholder="Enter the number">
                        </div>
                        <div class="form-group">
                            <label>Minigame:</label>
                            <input type="number" name="minigame" class="form-control" placeholder="Enter the minigame">
                        </div>
                        <div class="form-group">
                            <label>Treasure:</label>
                            <input type="number" name="treasure" class="form-control" placeholder="Enter the treasure">
                        </div>
                        <div class="form-group">
                            <label>Status:</label>
                            <select name="status" class="form-control">
                                <option value="">Please select status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- <div class="clues">
                            @include('admin.relics.clues.create', ['index'=> 0])
                            <input type="hidden" id="last-token" value="0">
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save</button>
                <!-- <button type="button" class="btn btn-danger" id="resetTheForm" onclick="document.getElementById('addRelicForm').reset()">Reset</button> -->
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.relics.scripts.relic-script')
<script>
    
    $(document).on('submit', '#addRelicForm', function(e) {

        let url = "{{ route('admin.relics.store', ':seasonID') }}";
        url = url.replace(":seasonID", $('select[name=season_id]').val());
        e.preventDefault();
        if(validate()) {
            $.ajax({
                type: "POST",
                url: url,
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
                            window.location.href = '{{ route('admin.relics.index') }}';
                        }, 2000)
                    } else {
                        toastr.warning(response.message);
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