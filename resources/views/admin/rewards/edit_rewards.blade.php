@csrf

<div class="modal-body">
    @if($huntReward->reward_type == 'gold' || $huntReward->reward_type == 'skeleton_key_and_gold' || $huntReward->reward_type == 'avatar_item_and_gold')
        <div class="form-group">
            <label>Gold:</label>
            <input type="number" class="form-control" placeholder="Enter gold" value="{{ $huntReward->gold_value }}" name="gold_value">
        </div>
    @endif
    <input type="hidden" name="id" id="reward_id" value="{{ $huntReward->id }}">
    <input type="hidden" name="reward_type" value="{{ $huntReward->reward_type }}">
    <!-- <div class="form-group">
        <label>Min range:</label>
        <input type="number" class="form-control" placeholder="Enter min range" value="{{ $huntReward->min_range }}" name="min_range">
    </div>
    <div class="form-group">
        <label>Max range:</label>
        <input type="number" class="form-control" placeholder="Enter max range" value="{{ $huntReward->max_range }}" name="max_range">
    </div> -->
    @if($huntReward->reward_type == 'skeleton_key' || $huntReward->reward_type == 'skeleton_key_and_gold')
    <div class="form-group">
        <label>Skeleton Key:</label>
        <input type="number" class="form-control" placeholder="Enter skeletons keys" value="{{ $huntReward->skeletons }}" name="skeletons">
    </div>
    @endif

    @if($huntReward->reward_type == 'avatar_item' || $huntReward->reward_type == 'avatar_item_and_gold')
        <h5>Widgets Order</h5>
        <div class="form-group">
            @foreach($huntReward->widgets_order as $widgetsOrder)
            <div class="row">
                <div class="col-md-6">
                    <label>Widget name: ({{ (($widgetsOrder['max']-$widgetsOrder['min'])+1)/10 }} %)</label>
                    <input type="hidden" name="min[]" value="{{ $widgetsOrder['min'] }}">
                    <input type="hidden" name="max[]" value="{{ $widgetsOrder['max'] }}">
                    <select name="widget_name[]" class="form-control">
                        @foreach($widgetItem as $key => $widget)
                            <option value="{{ $widget['widget_name'].'__'.$widget['widget_category'] }}" @if($widget['widget_name']==$widgetsOrder['widget_name'] && $widget['widget_category']==$widgetsOrder['type']){{ 'selected' }}@endif>{{ $widget['widget_name'].' ('.$widget['widget_category'].')' }}</option>
                        @endforeach
                    </select>  
                </div>
            </div>
            @endforeach
        </div>
    @endif
    <div class="clearfix"></div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success">Submit</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>