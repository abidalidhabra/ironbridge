<div class="single-clue-container" index="{{ $index+1 }}">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Clue Name:</label>
            <input 
            type="text" 
            class="form-control" 
            placeholder="Enter clue name"
            value="{{ old('clue_name.'.$index) }}" 
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
            name="hunts[{{$huntIndex}}][clues][{{$index}}][description]">{{ old('clue_name.'.$index) }}</textarea>
            @error('clue_description.'.$index)
            <div class="text-muted text-danger"> {{ $errors->first('clue_description.'.$index) }} </div>
            @enderror
        </div>
    </div>
    <div class="col-md-1">
        <button type="button" class="btn btn-success add-clue">+</button>
    </div>
</div>


{{--  <div class="single-html-container" index="{{ $index+1 }}">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Clue Name:</label>
            <input 
            type="text" 
            class="form-control" 
            placeholder="Enter clue name"
            name="clue_name[{{$index}}]">
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <label class="control-label">Clue Description:</label>
            <textarea 
            rows="5" 
            class="form-control" 
            placeholder="Enter clue description" 
            name="clue_description[{{$index}}]"></textarea>
        </div>
    </div>
    <div class="col-md-1">
        <button type="button" class="btn btn-success add-clue">+</button>
    </div>
</div>
 --}}