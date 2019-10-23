<div class="single-hunt-container col-md-12" index="{{ $index+1 }}">
    <div>
        <div class="col-md-11">
            <div class="form-group">
                <label class="control-label">Hunt Name:</label>
                <input 
                type="text" 
                class="form-control" 
                placeholder="Enter custom name" 
                name="hunts[{{$index}}][name]"
                alias-name="Hunt name"
                minlength="5" >
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
                    name="hunts[{{$index}}][clues][0][name]"
                    alias-name="Clue name"
                    minlength="5" >
                </div>
                <div class="form-group">
                    <label>Complexity:</label>
                    <select name="hunts[{{$index}}][clues][0][complexity]" class="form-control" alias-name="Hunt complexity" required>
                        <option value="">Select Complexity</option>
                        @for($i = 1; $i<=5; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label class="control-label">Clue Description:</label>
                    <textarea 
                    rows="5" 
                    class="form-control" 
                    placeholder="Enter clue description" 
                    name="hunts[{{$index}}][clues][0][description]"
                    alias-name="Clue description"
                    minlength="5"></textarea>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-success add-clue">+</button>
            </div>
        </div>
    </div>
</div>