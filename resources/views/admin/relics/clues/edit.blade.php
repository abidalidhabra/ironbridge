<div class="clue cluecontainer">
    <div class="colmd6box">
        <div class="form-group">
            <label class="control-label">Pieces Image:</label>
            <input 
            type="file" 
            class="form-control" 
            name="pieces[{{$index}}][image]" value="{{ public_path('storage/relics/'.$relic->complexity.'/'.$piece['image']) }}">
            <a data-fancybox="{{ $relic->name }}" href="{{ $piece['image'] }}">
                <img style="width: 80px;" src="{{ $piece['image'] }}" alt="Relic Image">
            </a>
            <input type="hidden" name="total_pieces[]">
        </div>
    </div>

    @if($last)
        <button type="button" class="btn btn-success add-clue">+</button>
        <button type="button" class="btn btn-danger remove-clue1 remove_pieces" data-apply="confirmation" data-id="{{ $piece['id'] }}">-</button>
    @else
        <button type="button" class="btn btn-danger remove-clue1 remove_pieces" data-apply="confirmation" data-id="{{ $piece['id'] }}">-</button>
    @endif
</div>