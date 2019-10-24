<div class="single-clue-container cluecontainer" index="{{ $index+1 }}">
    <div class="colmd6box">
        <div class="form-group">
            <label class="control-label">Clue Name:</label>
            <input 
            type="text" 
            class="form-control" 
            placeholder="Enter clue name"
            value="{{ $clue['name'] }}" 
            name="hunts[{{$parentIndex}}][clues][{{$index}}][name]"
            alias-name="Clue name"
            minlength="5" >
        </div>
        <div class="form-group">
            <label>Complexity:</label>
            <select name="hunts[{{$parentIndex}}][clues][{{$index}}][complexity]" class="form-control" alias-name="Hunt complexity" required>
                <option value="">Select Complexity</option>
                @for($i = 1; $i<=5; $i++)
                <option value="{{ $i }}" {{ ($i == $clue['complexity'])?'selected': '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>
    <div class="colmd5box">
        <div class="form-group">
            <label class="control-label">Clue Description:</label>
            <textarea 
            rows="5" 
            class="form-control" 
            placeholder="Enter clue description" 
            name="hunts[{{$parentIndex}}][clues][{{$index}}][description]"
            alias-name="Clue description"
            minlength="5">{{ $clue['description'] }}</textarea>
        </div>
    </div>

    @if($last)
        <button type="button" class="btn btn-success add-clue">+</button>
    @else
        <button type="button" class="btn btn-danger remove-clue">-</button>
    @endif
</div>