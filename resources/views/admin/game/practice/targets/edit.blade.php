<div class="clue cluecontainer targetsbox">
     <!-- <div class="col-md-3">
        <label>Target:</label>
        <select class="form-control select_target" name="select_target[]" data-id="{{ $index }}">
            <option value="">Select Targets</option>
            <option value="time" >Time</option>
            <option value="score">Score</option>
        </select>
    </div> -->
    <div class="col-md-3">
        <label>Score:</label>
        <input type="number" value="{{ (isset($targets['score'])?$targets['score']:'') }}" name="score[{{ $index }}]" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Time:</label>
        <input type="number" value="{{ (isset($targets['time'])?$targets['time']:'') }}" name="time[{{ $index }}]" class="form-control">
    </div>
    
    <div class="col-md-3">
        <label>XP:</label>
        <input type="number" name="xp[{{ $index }}]" value="{{ (isset($targets['xp'])?$targets['xp']:'') }}" class="form-control">
    </div>
    <div class="col-md-3">
        @if($last)
            <button type="button" class="btn btn-success add-target">+</button>
            <button type="button" class="btn btn-danger remove-target">-</button>
        @else
            <button type="button" class="btn btn-danger remove-target">-</button>
        @endif
    </div>
</div>