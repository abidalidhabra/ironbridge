<!-- <div class="form-group col-md-4">
	<label class="form-label">Row</label>
	<input type="text" name="row[{{$index['current_index']}}][{{$index['game_index']}}]" id="row" class="form-control">
</div>
<div class="form-group col-md-4">
	<label class="form-label">Column</label>
	<input type="text"  name="column[{{$index['current_index']}}][{{$index['game_index']}}]" id="column" class="form-control">
</div> -->
	<input type="hidden"  name="column[{{$index['current_index']}}][{{$index['game_index']}}]" value="10" class="form-control">
	<input type="hidden" name="row[{{$index['current_index']}}][{{$index['game_index']}}]" value="10" class="form-control">
<div class="form-group col-md-4">
	<label class="form-label">Number Generate</label>
	<input type="text" name="number_generate[{{$index['current_index']}}][{{$index['game_index']}}]" id="number_generate" class="form-control">
</div>