<div class="single-clue-container" index="{{ $index+1 }}">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Clue Name:</label>
            <input 
            type="text" 
            class="form-control" 
            placeholder="Enter clue name"
            value="{{ $clue['name'] }}" 
            name="hunts[{{$huntIndex}}][clues][{{$index}}][name]">
            @error('clue_name.'.$index)
            <div class="text-muted text-danger"> {{ $errors->first('clue_name.'.$index) }} </div>
            @enderror
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <label class="control-label">Clue Description:</label>
            <textarea 
            rows="5" 
            class="form-control" 
            placeholder="Enter clue description" 
            name="hunts[{{$huntIndex}}][clues][{{$index}}][description]">{{ $clue['description'] }}</textarea>
            @error('clue_description.'.$index)
            <div class="text-muted text-danger"> {{ $errors->first('clue_description.'.$index) }} </div>
            @enderror
        </div>
    </div>
    <div class="col-md-1">
        <button type="button" class="btn btn-success add-clue">+</button>
    </div>
</div>