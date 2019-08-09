<div class="form-group col-md-4">
	<label class="form-label">Row <small class="form-text text-muted">must of [4,6,8]</small></label>
	<input type="text" name="row[{{$index['current_index']}}][{{$index['game_index']}}]" id="row" class="form-control">
</div>
<div class="form-group col-md-4">
	<label class="form-label">Column <small class="form-text text-muted">must of [4,6,8]</small></label>
	<input type="text"  name="column[{{$index['current_index']}}][{{$index['game_index']}}]" id="column" class="form-control">
</div>
<div class="form-group col-md-4">
	<label class="form-label">Target <small class="form-text text-muted">must of [1024,2048,4096]</small></label>
	<input type="text"  name="target[{{$index['current_index']}}][{{$index['game_index']}}]" id="target" class="form-control">
</div>