<div class="clue cluecontainer targetsbox">
    <div class="col-md-3">
        <label>Score:</label>
        <input type="number" value="" name="score[{{ $index }}]" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Time:</label>
        <input type="number" value="" name="time[{{ $index }}]" class="form-control">
    </div>
    
    <div class="col-md-3">
        <label>XP:</label>
        <input type="number" name="xp[{{ $index }}]" class="form-control">
    </div>
    <div class="col-md-3">
        <button type="button" class="btn btn-success add-target">+</button>
        <button type="button" class="btn btn-danger remove-target">-</button>
    </div>
    
</div>