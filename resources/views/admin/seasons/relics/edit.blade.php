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
                            <label class="control-label">Season Name:</label>
                            <select class="form-control" name="season_id">
                                @forelse($seasons as $season)
                                <option 
                                type="text" 
                                class="form-control" 
                                value="{{ $season->id }}" 
                                @if($season->id == $relic->season->id) selected @endif>{{ $season->name }}</option>
                                @empty
                                <option type="text" class="form-control" value="">No seasons</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="form-group @error('relic_name') has-error @enderror">
                            <label class="control-label">Relic Name:</label>
                            <input 
                            type="text" 
                            class="form-control" 
                            placeholder="Enter relic name" 
                            name="relic_name" 
                            value="{{ $relic->name }}"
                            alias-name="Relic name"
                            required>
                            @error('relic_name')
                            <div class="text-muted text-danger"> {{ $errors->first('relic_name') }} </div>
                            @enderror
                        </div>
                        
                        <div class="form-group @error('relic_desc') has-error @enderror">
                            <label class="control-label">Relic Description:</label>
                            <textarea 
                            rows="5" 
                            class="form-control" 
                            placeholder="Enter relic description" 
                            name="relic_desc"
                            alias-name="Relic description"
                            minlength="5">{{ $relic->desc }}</textarea>
                            @error('relic_desc')
                            <div class="text-muted text-danger"> {{ $errors->first('relic_desc') }} </div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group @error('icon') has-error @enderror">
                                    <label class="control-label">Active icon for relic:</label>
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
                            <div class="col-md-4">
                                <div class="form-group checkbox @error('active') has-error @enderror">
                                    <label><input type="checkbox" name="active" value="true" {{ ($relic->active)? 'checked': '' }}>Active</label>
                                    @error('active')
                                    <div class="text-muted text-danger"> {{ $errors->first('active') }} </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group @error('fees') has-error @enderror">
                            <label class="control-label">Fees:</label>
                            <input 
                            type="text" 
                            class="form-control" 
                            name="fees"
                            value="{{ $relic->fees }}"
                            alias-name="Fees for relic"
                            required>
                            @error('fees')
                            <div class="text-muted text-danger"> {{ $errors->first('fees') }} </div>
                            @enderror
                        </div>

             {{--            <div class="form-group @error('inactive_icon') has-error @enderror">
                            <label class="control-label">Active icon for relic:</label>
                            <input 
                            type="file" 
                            class="form-control" 
                            name="inactive_icon" 
                            alias-name="Active icon for relic">
                            <b><a href="{{ asset('storage/seasons/'.$relic->season->id.'/'.$relic->inactive_icon) }}" target="_blank">VIEW</a></b>
                            @error('inactive_icon')
                            <div class="text-muted text-danger"> {{ $errors->first('inactive_icon') }} </div>
                            @enderror
                        </div> --}}

                       

                        <div class="form-group">
                            <label>Relic Complexity:</label>
                            <select name="complexity" class="form-control" alias-name="Relic complexity" required>
                                <option value="">Select Complexity</option>
                                @for($i = 1; $i<=5; $i++)
                                <option value="{{ $i }}" {{ ($i == $relic->complexity)?'selected': '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="clues">
                            @forelse($relic->clues as $index=> $clue)
                                @php $lastIndex = $index; @endphp
                                @include('admin.seasons.relics.clues.edit', ['index'=> $index, 'clue'=> $clue, 'last'=> $loop->last])
                            @empty
                                <h4 class="text-danger">No clue found in this relic.</h4>
                            @endforelse
                            <input type="hidden" id="last-token" value="{{ $lastIndex ?? 0 }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save</button>
                <button type="button" class="btn btn-danger" id="resetTheForm" onclick="document.getElementById('addRelicForm').reset()">Reset</button>
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
                success: function(response)
                {
                    if (response.status == true) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = '{{ route('admin.relics.index') }}';
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