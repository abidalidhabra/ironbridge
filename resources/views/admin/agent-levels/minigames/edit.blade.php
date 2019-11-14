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
                <label class="control-label">Minigames:</label>
                <select name="minigames[]" class="form-control minigames"  multiple="multiple" style="width: 100%;">
                    <option value="">Select Minigames</option>
                    @forelse($games as $game)
                    <option value="{{ $game->id }}" @if(in_array($game->id,$agent_data->minigames)){{ 'selected' }}@endif>{{ $game->name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success">Save</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>