<div class="widget">
    <div class="col-md-12">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Widgets:</label>
                <select name="avatar_item[widgets_order][widget_name][{{$parent_index}}][{{$current_index}}]" class="form-control">
                    @foreach($widgetItem as $key => $widget)
                        <option value="{{ $widget['widget_name'].'__'.$widget['widget_category'] }}">{{ $widget['widget_name'].' ('.$widget['widget_category'].')' }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Possibility:</label>
                <input type="number" class="form-control" placeholder="Enter possibility" name="avatar_item[widgets_order][possibility][{{$parent_index}}][{{$current_index}}]">
            </div>
        </div>
        <button type="button" class="btn btn-success plus-reward-icon add-widget">Add widget</button>
    </div>
</div>