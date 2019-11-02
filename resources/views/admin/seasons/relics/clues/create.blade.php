<div class="clue cluecontainer">
    
    <div class="colmd6box">
        <div class="form-group">
            <label class="control-label">Clue Name:</label>
            <input 
            type="text" 
            class="form-control" 
            placeholder="Enter clue name"
            name="clues[{{$index}}][name]"
            alias-name="Clue name"
            minlength="5" 
            required>
        </div>
        <div class="form-group">
            <label class="control-label">Clue Radius:</label>
            <input 
            type="text" 
            class="form-control" 
            placeholder="Enter clue radius"
            name="clues[{{$index}}][radius]"
            alias-name="Clue radius">
        </div>
    </div>
    
    <div class="colmd5box">
        <div class="form-group">
            <label class="control-label">Clue Description:</label>
            <textarea 
            rows="5" 
            class="form-control" 
            placeholder="Enter clue description" 
            name="clues[{{$index}}][desc]"
            alias-name="Clue description"
            minlength="5"></textarea>
        </div>
    </div>
    <button type="button" class="btn btn-success add-clue">+</button>
</div>