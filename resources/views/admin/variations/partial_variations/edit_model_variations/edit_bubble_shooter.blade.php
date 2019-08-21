<input type="hidden" name="sudoku_id" value="0">
<input type="hidden" name="number_generate" value="0">
<input type="hidden" name="row" value="0">
<input type="hidden" name="column" value="0">
<input type="hidden" name="variation_size" value="0">


<div class="form-group col-md-6">
    <label class="form-label">Target</label>
    <input type="text" value="{{ $variations->target }}" name="target" id="target" class="form-control">
</div>
<div class="form-group col-md-6">
    <label class="form-label">No Of balls</label>
    <input type="text"  value="{{ $variations->no_of_balls }}" name="no_of_balls" id="no_of_balls" class="form-control">
</div>
<div class="form-group col-md-6">
    <label class="form-label">Bubble level id</label>
    <input type="text"  value="{{ $variations->bubble_level_id }}" name="bubble_level_id" id="bubble_level_id" class="form-control">
</div>