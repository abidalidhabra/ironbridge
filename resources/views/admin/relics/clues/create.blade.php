<div class="clue cluecontainer">
    
    <div class="colmd6box">
        <div class="form-group">
            <label class="control-label">Pieces Image:</label>
            <input 
            type="file" 
            class="form-control" 
            name="pieces[{{$index}}][image]" 
            required>
        </div>
    </div>
    <input type="hidden" name="total_pieces[]">
    <button type="button" class="btn btn-success add-clue">+</button>
</div>