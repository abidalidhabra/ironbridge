<div class="avatar adlotdetacover" index="{{ $index }}">
    <div class="col-md-5">
        <div class="form-group">
            <label class="control-label">Possibility:</label>
            <input 
            type="number" 
            class="form-control" 
            placeholder="Enter possibility"
            name="avatar_item[possibility][{{$index}}]">
        </div>
    </div>
    <button type="button" class="btn btn-success add-avatar">+</button>
    <div class="widgets">
        @include('admin.loots.reward.widget', ['parent_index'=> $index, 'current_index'=> 0])
    </div>
</div>