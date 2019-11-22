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
                    <h3>Edit Relic</h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <form method="POST" id="addRelicForm" action="{{ route('admin.relics.update', $relic->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-body padboxset">
                <div class="modalbodysetbox">
                    <div class="addrehcover">
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter the name" value="{{ $relic->name }}">
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group @error('icon') has-error @enderror">
                                    <label class="control-label">Active Image for relic:</label>
                                    <input 
                                    type="file" 
                                    class="form-control" 
                                    name="icon" 
                                    alias-name="Icon for relic">
                                    @error('icon')
                                    <div class="text-muted text-danger"> {{ $errors->first('icon') }} </div>
                                    @enderror
                                    
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="img-container imgiconboxsetiner">
                                    <p>Icon</p>
                                    <a data-fancybox="{{ $relic->name }}" href="{{ $relic->icon }}">
                                        <img style="width: 80px;" src="{{ $relic->icon }}" alt="{{ $relic->name }} relic icon">
                                    </a>
                                </div>
                            </div>
                            
                        </div>

                                      

                        <div class="form-group">
                            <label>TH Complexity:</label>
                            <select name="complexity" class="form-control" alias-name="Relic complexity" required>
                                <option value="">Select TH Complexity</option>
                                @for($i = 1; $i<=5; $i++)
                                <option value="{{ $i }}" {{ ($i == $relic->complexity)?'selected': '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Relic Map Pieces:</label>
                            <input type="number" name="pieces" class="form-control" placeholder="Enter the relic map pieces" value="{{ $relic->pieces }}">
                        </div>
                        <div class="form-group">
                            <label>Number:</label>
                            <input type="number" name="number" class="form-control" placeholder="Enter the number" value="{{ $relic->number }}">
                        </div>
                        <div class="form-group">
                            <label>Loot Number:</label>
                            <select name="loot_table_number" class="form-control">
                                <option value="">Select Loot Number</option>
                                @forelse($loots as $key => $value)
                                <option value="{{ $key }}" {{ ($key == $relic->loot_table_number)?'selected': '' }}>{{ $key }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status:</label>
                            <select name="status" class="form-control">
                                <option value="">Please select status</option>
                                <option value="active" {{ ($relic->active == true)?'selected': '' }}>Active</option>
                                <option value="inactive" {{ ($relic->active == false)?'selected': '' }}>Inactive</option>
                            </select>
                        </div>
                        
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
@include('admin.seasons.scripts.relic-script')

<script>
    $(document).on('submit', '#addRelicForm', function(e) {
        e.preventDefault();
        if(validate()) {
            $.ajax({
                type: "POST",
                url: '{{ route('admin.relics.update', $relic->id) }}',
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

    $(".remove_pieces").confirmation({
            container:"body",
            btnOkClass:"btn btn-sm btn-success",
            btnCancelClass:"btn btn-sm btn-danger",
            onConfirm:function(event, element) {
                var pieces_id = element.attr('data-id');
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("admin.removePieces") }}',
                    data: {pieces_id : pieces_id,id:'{{ $relic->id }}'},
                    success: function(response)
                    {
                        if (response.status == true) {
                            toastr.success(response.message);
                            location.reload(true);
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            }
        });
</script>
@endsection