@section('title','Ironbridge1779 | Map Pieces Loot')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="customdatatable_box" id="formContainer">
        <div class="users_datatablebox">
            <h3>Map Pieces Loot</h3>
        </div>
        <form method="POST" id="updateMapPieceLootForm" class="appstbboxcover">
            @csrf
            @method('PUT')
            <div class="appstbboxin">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Probability To Give Map Piecees in %:</label>
                            <input type="number" name="probability" class="form-control" value="{{ $huntStatistic->map_pieces['max'] }}" min="0" placeholder="Probability To Give Map Piecees in %">
                        </div>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </div>
          </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')

<script>
    $('[data-toggle="tooltip"]').tooltip(); 
    $(document).on('submit', '#updateMapPieceLootForm', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '{{ route("admin.map-pieces-loots.update") }}',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response){
                toastr.success(response.message);
            },
            error: function(jqXHR, exception) {
                toastr.error(errHandling(jqXHR, exception));
            }
        });
    });
</script>
@endsection