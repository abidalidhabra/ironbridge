@csrf
@method('PUT')
<div class="modal-body">
    <div class="modalbodysetbox">
        <input type="hidden" name="agent_level_id" id="agent_level_id" value="{{ $agent_data->id }}">
        <div class="newstitlebox_inputbox">
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
        <div class="newstitlebox_inputbox">
            <div class="form-group">
                <label class="control-label">TH Difficulty:</label>
                <select name="complexity" class="form-control">
                    <option value="">Select TH Difficulty</option>
                    @for($i=1;$i <= 5;$i++)
                        <option value="{{ $i }}" @if($i==$agent_data->complexity){{ 'selected' }}@endif>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success">Save</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>