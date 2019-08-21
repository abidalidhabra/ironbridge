<?php
	if (isset($variation_data)) {
		print_r($variation_data);
		exit();
	}
?>
<div class="form-group col-md-4">
    <label class="form-label">Variation size <small class="form-text text-muted">must of [12,35,70,140]</small></label>
    <input type="text"  name="variation_size" value="" id="variationSize" class="form-control">
</div>
<div class="form-group col-md-4">
	<label class="form-label">Variation Image <small class="form-text text-muted">must be 2000*1440 dimension</small></label>
	<input type="file"  name="variation_image[]" id="variation_image" class="form-control" multiple>
	<br/>
</div>
