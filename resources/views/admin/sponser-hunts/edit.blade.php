@section('title','Ironbridge1779 | Sponser Hunt Creation')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="">               
            <div class="col-md-12 text-right">
                <a href="{{ route('admin.mapsList') }}" class="btn back-btn">Back</a>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <h3>Add Sponser Hunt</h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <form method="POST" id="addSponserHunt" action="{{ route('admin.sponser-hunts.update', $season->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-body padboxset">
                <div class="modalbodysetbox">
                    <div class="form-group">
                        <label class="control-label">Season Name:</label>
                        <input 
                        type="text" 
                        class="form-control" 
                        placeholder="Enter custom name",
                        value="{{ $season->name }}" 
                        name="season_name" 
                        id="season_name">
                        @error('season_name')
                        <div class="text-muted text-danger"> {{ $errors->first('season_name') }} </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Complexity:</label>
                        <select name="complexity" class="form-control">
                            <option value="">Select Complexity</option>
                            @for($i = 1; $i<=5; $i++)
                            <option value="{{ $i }}" {{ ($season->complexity == $i)? 'selected': '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" name="active" id="active" value="true" {{ ($season->active)? "checked": "" }}>Active</label>
                    </div>
                    <div class="col-md-12">
                        <h4>Hunts</h4>
                        <div class="hunt-container">
                            @forelse($season->hunts as $index=> $hunt)
                                @include('admin.sponser-hunts.partials.hunt-edit', ['index'=> $index, 'hunt'=> $hunt])
                            @empty
                                <h4 class="text-danger">No Hunt Found.</h4>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save</button>
                <button type="button" class="btn btn-danger btn-cancel">Cancel</button>
                <input type="button" id="resetPolygon" value="Reset" style="display: none;" />
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script>

        /** Add Hunt **/
        let huntURL = "{{ route('admin.sponser-hunts.hunt-html') }}"
        $(document).on('click', '.add-hunt', function() {
            let lastIndex = parseInt($('.hunt-container').children().last().attr('index'));
            $.get(huntURL, {index: lastIndex}, function(response) {
                $('.hunt-container').append(response);
            });
            $(this).removeClass('btn-success add-hunt').addClass('btn-danger remove-hunt').text('-');
        });
        $(document).on('click', '.remove-hunt', function() {
            $(this).parent().parent().remove();
        });


        /** Add Clue **/
        let clueURL = "{{ route('admin.sponser-hunts.clue-html') }}"
        $(document).on('click', '.add-clue', function() {
            let huntElem = $(this).parents().closest('.single-hunt-container');
            let huntIndex = parseInt(huntElem.attr('index')-1);
            let lastIndex = parseInt($('.clue-container').children().last().attr('index'));
            $.get(clueURL, {index: lastIndex, huntIndex: huntIndex}, function(response) {
                $(huntElem).children('.clue-container').append(response);
            });
            $(this).removeClass('btn-success add-clue').addClass('btn-danger remove-clue').text('-');
        });
        $(document).on('click', '.remove-clue', function() {
            $(this).parent().parent().remove();
        });
    </script>
@endsection