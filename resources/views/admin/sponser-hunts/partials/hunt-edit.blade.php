<div class="single-hunt-container inerboxallgnhut" index="{{ $index+1 }}">
    <div class="huntnameboxlr">
        <div class="huntnamelr_left">
            <div class="form-group">
                <label class="control-label">Hunt Name:</label>
                <input 
                type="text" 
                class="form-control" 
                placeholder="Enter custom name"
                value="{{ $hunt->name }}" 
                name="hunts[{{$index}}][name]" 
                alias-name="Hunt name"
                minlength="5" >
            </div>
        </div>
        @if($last)
            <button type="button" class="btn btn-success add-hunt">+</button>
        @else
            <button type="button" class="btn btn-danger remove-hunt">-</button>
        @endif
    </div>
    <div class="clue-container">
        @forelse($hunt->clues as $clueIndex=> $clue)
            @include('admin.sponser-hunts.partials.clue-edit', ['index'=> $clueIndex, 'clue'=> $clue, 'parentIndex'=> $index, 'last'=> $loop->last])
        @empty
            <h4 class="text-danger">No Clue Found.</h4>
        @endforelse
    </div>
</div>