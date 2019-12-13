@csrf
@method('PUT')
<div class="modal-body">
    <div class="modalbodysetbox">
        <input type="hidden" name="agent_level_id" id="agent_level_id" value="{{ $agent_data->id }}">
        <div class="">
            <div class="form-group">
                <label class="control-label">Agent Level:</label>
                <select name="agent_level" class="form-control">
                    <option>Select Agent Level</option>
                    @forelse($agent_complementary as $agent)
                        <option value="{{ $agent->agent_level }}" @if($agent->agent_level==$agent_data->agent_level){{ 'selected' }}@endif >{{ $agent->agent_level }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>       
        <div class="">
            <div class="form-group">
                <label class="control-label">Special Ability:</label>
                <select name="nodes[]" class="form-control nodes"  multiple="multiple" style="width: 100%;">
                    <option value="mg_challenge" @if($agent_data->nodes && isset($agent_data->nodes['mg_challenge'])){{ 'selected' }}@endif>Mini-game Challenge Nodes</option>
                    <option value="power" @if($agent_data->nodes && isset($agent_data->nodes['power'])){{ 'selected' }}@endif>Power Nodes</option>
                    <option value="bonus" @if($agent_data->nodes && isset($agent_data->nodes['bonus'])){{ 'selected' }}@endif>Bonus Nodes</option>
                </select>
            </div>
            <div class="form-group power_box" style="display: @if($agent_data->nodes && isset($agent_data->nodes['power']) && isset($agent_data->nodes['power']['value'])){{ '' }}@else {{'none'}}@endif ">
                <label class="control-label">Power Value:</label>
                <input type="number" name="power" value="{{ (isset($agent_data->nodes['power']['value'])?$agent_data->nodes['power']['value']:'') }}" class="form-control">
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success">Save</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>