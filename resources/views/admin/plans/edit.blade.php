<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Edit Plans</h4>
        </div>
        <form method="POST" id="editPlansForm">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="hidden" id="plan_id" name="plan_id" value="{{ $plan->id }}">
                        <label class="control-label">Name:</label>
                        <input type="text" name="name" class="form-control" value="{{ $plan->name }}" placeholder="Enter the name"  autocomplete="off">
                    </div>
                </div>
                <div class="col-md-6 hide">
                    <div class="form-group">
                        <label class="control-label">Country:</label>
                        <select class="form-control" disabled>
                            @foreach($countries as $country)
                            <option @if($plan->country_id == $country->id) {{ 'selected' }} @endif>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if($plan->type == 'gold')
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Price:</label>
                        <input type="text" name="price" class="form-control" value="{{ $plan->price }}" placeholder="Enter the price"  autocomplete="off">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Gold Value:</label>
                        <input type="text" name="gold_value" class="form-control" value="{{ $plan->gold_value }}" placeholder="Enter the gold value">
                    </div>
                </div>
                @endif
                <div class="col-md-6 hide">
                    <div class="form-group">
                        <label class="control-label">Type:</label>
                        <select name="type" class="form-control" disabled="">
                            <option value="gold" @if($plan->type == 'gold') {{ 'selected' }} @endif>Gold</option>
                            <option value="skeleton" @if($plan->type == 'skeleton') {{ 'selected' }} @endif>skeleton</option>
                            <option value="chest_bucket" @if($plan->type == 'chest_bucket') {{ 'selected' }} @endif>Chest Bucket</option>
                            <option value="compass" @if($plan->type == 'compass') {{ 'selected' }} @endif>Compass</option>
                        </select>
                    </div>
                </div>

                @if($plan->type == 'skeleton_bucket')
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">skeletons bucket:</label>
                        <input type="text" name="skeletons_bucket" class="form-control" value="{{ $plan->skeletons_bucket }}" placeholder="Enter the skeletons bucket">
                    </div>
                </div>
                @endif

                @if($plan->type == 'skeleton' || $plan->type == 'skeleton_bucket' || $plan->type == 'chest_bucket' || $plan->type == 'compass')
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Gold Price:</label>
                            <input type="text" name="gold_price" class="form-control" value="{{ $plan->gold_price }}" placeholder="Enter the gold price">
                        </div>
                    </div>
                @endif
                
                @if($plan->type == 'skeleton')
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Skeleton Keys:</label>
                            <input type="text" name="skeleton_keys" class="form-control" value="{{ $plan->skeleton_keys }}" placeholder="Enter the skeleton keys">
                        </div>
                    </div>
                @endif

                @if($plan->type == 'chest_bucket')
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Bucket:</label>
                            <input type="text" name="bucket" class="form-control" value="{{ $plan->bucket }}" placeholder="Enter the bucket">
                        </div>
                    </div>
                @endif

                @if($plan->type == 'compass')
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Compasses:</label>
                            <input type="text" name="compasses" class="form-control" value="{{ $plan->compasses }}" placeholder="Enter the compasses">
                        </div>
                    </div>
                @endif
            </div>
            <div style="clear: both;"></div>
            <div class="modal-footer">
                <button class="commonBtn btn btn-success" type="submit">Save</button>
                <button type="button" class="btn btn-default commonBtn" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>
