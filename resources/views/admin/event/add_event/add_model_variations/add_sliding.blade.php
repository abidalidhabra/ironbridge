<div class="form-group col-md-4">
	<label class="form-label">Variation size <small class="form-text text-muted">must of [4,5,6]</small></label>
	<input type="text"  name="variation_size[{{$index['current_index']}}][{{$index['game_index']}}]" id="variationSize" class="form-control">
</div>

<div class="form-group col-md-4">
	<label class="form-label">Variation Image <small class="form-text text-muted">must be 1024*1024 dimension</small></label>
	<input type="file"  name="variation_image[{{$index['current_index']}}][{{$index['game_index']}}]" class="form-control">
</div>
