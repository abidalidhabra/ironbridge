<div class="single-hunt-container col-md-12" index="{{ $index+1 }}">
    <div>
        <div class="col-md-11">
            <div class="form-group">
                <label class="control-label">Hunt Name:</label>
                <input 
                type="text" 
                class="form-control" 
                placeholder="Enter custom name" 
                name="hunts[{{$index}}][name]" >
            </div>
        </div>
        <button type="button" class="btn btn-success add-hunt">+</button>
    </div>
    <div class="clue-container">
        <div class="single-clue-container" index="1">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Clue Name:</label>
                    <input 
                    type="text" 
                    class="form-control" 
                    placeholder="Enter clue name"
                    value="{{ old('clue_name.'.$index) }}" 
                    name="hunts[{{$index}}][clues][0][name]">
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
                    name="hunts[{{$index}}][clues][0][description]">{{ old('clue_name.'.$index) }}</textarea>
                    @error('clue_description.'.$index)
                    <div class="text-muted text-danger"> {{ $errors->first('clue_description.'.$index) }} </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-success add-clue">+</button>
            </div>
        </div>
    </div>
</div>