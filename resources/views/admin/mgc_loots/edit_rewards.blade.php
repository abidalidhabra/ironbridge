@csrf

<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">Please select status</option>
                    <option value="active" {{ ($loot[0]->status == true)?'selected': '' }}>Active</option>
                    <option value="inactive" {{ ($loot[0]->status == false)?'selected': '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>
    @if($loot[0]->relics_info)
        @php
            $relicIds = $loot[0]->relics_info->pluck('id')->toArray(); 
        @endphp
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Relics</label>
                    <br/>
                    <select name="relics[]" class="form-control" id="relics" multiple>
                        @forelse($relics as $relic)
                            <option value="{{ $relic->id }}" {{ (in_array($relic->id,$relicIds))?'selected': '' }}>{{ $relic->name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>
        </div>
    @endif
    @forelse($lootReward as $key => $reward)
        <h4>{{ ucwords(str_replace('_',' ',$key)) }}</h4>
        @if($key == 'gold' || $key == 'skeleton_key' || $key == 'avatar_item' || $key == 'avatar_item_and_gold')
            <div class="row">
                <div class="col-md-6">
                    <label>Possibility</label>
                </div>
                <div class="col-md-6">
                    @if($key == 'gold' || $key == 'avatar_item_and_gold')
                        <label>Gold</label>
                    @elseif($key == 'skeleton_key')
                        <label>skeleton key</label>
                    @endif
                </div>
            </div>
        @endif
        @if($key == 'skeleton_key_and_gold')
            <div class="row">
                <div class="col-md-4">
                    <label>Possibility</label>
                </div>
                <div class="col-md-4">
                    <label>Gold</label>
                </div>
                <div class="col-md-4">
                    <label>skeleton key</label>
                </div>
            </div>
        @endif
        @foreach($reward as $rewardvalue)
            @if($rewardvalue['reward_type'] == 'gold')
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Enter possibility" value="{{ $rewardvalue['possibility'] }}" name="possibility[{{ $rewardvalue->id }}]">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit">%</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="number" class="form-control" placeholder="Enter gold" value="{{ $rewardvalue['gold_value'] }}" name="gold_value[{{ $rewardvalue->id }}]">
                        </div>
                    </div>
                </div>
            @endif

            @if($rewardvalue['reward_type'] == 'skeleton_key')
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Enter possibility" value="{{ $rewardvalue['possibility'] }}" name="possibility[{{ $rewardvalue->id }}]">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit">%</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="number" class="form-control" placeholder="Enter skeletons" value="{{ $rewardvalue['skeletons'] }}" name="skeletons[{{ $rewardvalue->id }}]">
                        </div>
                    </div>
                </div>
            @endif
            @if($rewardvalue['reward_type'] == 'skeleton_key_and_gold')
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Enter possibility" value="{{ $rewardvalue['possibility'] }}" name="possibility[{{ $rewardvalue->id }}]">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit">%</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="number" class="form-control" placeholder="Enter gold" value="{{ $rewardvalue['gold_value'] }}" name="gold_value[{{ $rewardvalue->id }}]">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="number" class="form-control" placeholder="Enter skeletons" value="{{ $rewardvalue['skeletons'] }}" name="skeletons[{{ $rewardvalue->id }}]">
                        </div>
                    </div>
                </div>
            @endif
            @if($rewardvalue['reward_type'] == 'avatar_item')
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Enter possibility" value="{{ $rewardvalue['possibility'] }}" name="possibility[{{ $rewardvalue->id }}]">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit">%</button>
                            </div>
                        </div>
                    </div>
                </div>
                <label>Widgets Order</label>
                <div class="row">
                    <div class="col-md-6">
                        <label>Possibility</label>
                    </div>
                    <div class="col-md-6">
                        <label>Widgets</label>
                    </div>
                </div>
                @foreach($rewardvalue['widgets_order'] as $widgetsOrder)
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Enter possibility" value="{{ (isset($widgetsOrder['possibility']))?$widgetsOrder['possibility']:'' }}" name="widgets_possibility[{{ $rewardvalue->id }}][]">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit">%</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <select name="widget_name[{{ $rewardvalue->id }}][]" class="form-control">
                                @foreach($widgetItem as $key => $widget)
                                    <option value="{{ $widget['widget_name'].'__'.$widget['widget_category'] }}" @if($widget['widget_name']==$widgetsOrder['widget_name'] && $widget['widget_category']==$widgetsOrder['type']){{ 'selected' }}@endif>{{ $widget['widget_name'].' ('.$widget['widget_category'].')' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
            @if($rewardvalue['reward_type'] == 'avatar_item_and_gold')
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Enter possibility" value="{{ $rewardvalue['possibility'] }}" name="possibility[{{ $rewardvalue->id }}]">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit">%</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="number" class="form-control" placeholder="Enter gold" value="{{ $rewardvalue['gold_value'] }}" name="gold_value[{{ $rewardvalue->id }}]">
                        </div>
                    </div>
                </div>
                <label>Widgets Order</label>
                <div class="row">
                    <div class="col-md-6">
                        <label>Possibility</label>
                    </div>
                    <div class="col-md-6">
                        <label>Widgets</label>
                    </div>
                </div>
                @foreach($rewardvalue['widgets_order'] as $widgetsOrder)
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Enter possibility" value="{{ $widgetsOrder['possibility'] }}" name="widgets_possibility[{{ $rewardvalue->id }}][]">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit">%</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <select name="widget_name[{{ $rewardvalue->id }}][]" class="form-control">
                                @foreach($widgetItem as $key => $widget)
                                    <option value="{{ $widget['widget_name'].'__'.$widget['widget_category'] }}" @if($widget['widget_name']==$widgetsOrder['widget_name'] && $widget['widget_category']==$widgetsOrder['type']){{ 'selected' }}@endif>{{ $widget['widget_name'].' ('.$widget['widget_category'].')' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        @endforeach
    @empty
    @endforelse
    <div class="clearfix"></div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success">Submit</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>