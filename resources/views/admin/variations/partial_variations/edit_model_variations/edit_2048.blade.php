<input type="hidden" name="sudoku_id" value="0">
<input type="hidden" name="number_generate" value="0">
<input type="hidden" name="no_of_balls" value="0">
<input type="hidden" name="bubble_level_id" value="0">
<input type="hidden" name="variation_size" value="0">


<div class="form-group col-md-6">
    <label class="form-label">Row <small class="form-text text-muted">must of [4,6,8]</small></label>
    <input type="text" value="{{ $variations->row }}" name="row" id="row" class="form-control">
</div>
<div class="form-group col-md-6">
    <label class="form-label">Column <small class="form-text text-muted">must of [4,6,8]</small></label>
    <input type="text" value="{{ $variations->column }}" name="column" id="column" class="form-control">
</div>
<div class="form-group col-md-6">
    <label class="form-label">Target <small class="form-text text-muted">must of [512,1024,2048,4096]</small></label>
    <input type="text" value="{{ $variations->target }}" name="target" id="target" class="form-control">
</div>