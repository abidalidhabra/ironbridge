@section('title','Ironbridge1779 | Joke Items Loot')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="customdatatable_box" id="JokeItemsProbabilityContainer">
        <div class="users_datatablebox">
            <h3>Joke Item Loot</h3>
        </div>
        <form method="POST" id="updateJokeItemProbabilityForm" class="appstbboxcover">
            @csrf
            @method('PUT')
            <div class="appstbboxin">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">Probability To Give Joke Items in %:</label>
                            <input type="number" name="probability" class="form-control" value="{{ $huntStatistic->joke_item['max'] }}" min="0" placeholder="Probability To Give Joke Items in %" required>
                        </div>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </div>
          </div>
        </form>
    </div>

    <div class="customdatatable_box" id="JokeItemsContainer">
        <div class="users_datatablebox">
            <h3>Joke Item</h3>
        </div>
        <form method="POST" id="updateJokeItemForm" class="appstbboxcover">
            @csrf
            @method('PUT')
            <div class="avtarimgtextiner">
                <img class="card-img-top" id="thumb-1" src="{{ $items[0]->image }}">
                <div class="card-body">
                    <div class="col-md-8">
                        <div class="row">
                            <h5 class="card-title" id="itemName">{{$items[0]->name}}</h5>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="row">
                            <a href="javascript:void(0)" id="editTheItem" data-id="{{$items[0]->id}}" class="widget_edit"><i class="fa fa-pencil iconsetaddbox"></i></a>    
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container">  
    <div class="addnewsmodal_box">
        <div class="modal fade" id="editFakeItemModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Joke Item</h4>
                    </div>
                    <form method="POST" id="editFakeItemForm">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="modalbodysetbox">
                                <div class="col-md-6">
                                    <div class="newstitlebox_inputbox">
                                        <div class="form-group">
                                            <label class="form-label">Item Name</label>
                                            <input type="text" class="form-control" name="name" placeholder="Item Name" autocomplete="off" required>
                                            <input type="hidden" class="form-control" name="id">
                                        </div>
                                    </div>       
                                </div>
                                <div class="col-md-6">
                                    <div class="newstitlebox_inputbox">
                                        <div class="form-group">     
                                            <label class="form-label">Item Image</label>
                                            <input type="file" class="form-control" name="image">
                                            <input type="hidden" id="previewSrc" class="form-control" value="">
                                            <input type="hidden" id="haveImage" class="form-control" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    $('[data-toggle="tooltip"]').tooltip(); 
    
    $(document).on('click', '#editTheItem', function(e) {
        let itemId = $(this).attr('data-id');
        let url = '{{ route("admin.joke-items-loots.edit", ":id") }}';
        url = url.replace(':id', itemId);
        $.ajax({
            type: "GET",
            url: url,
            dataType: 'json',
            success: function(response){
                $('#editFakeItemForm [name="name"]').val(response.item.name);
                $('#editFakeItemForm [name="id"]').val(response.item._id);
                $('#editFakeItemModal').modal('show');
            },
            error: function(jqXHR, exception) {
                toastr.error(errHandling(jqXHR, exception));
            }
        });
    });

    $(document).on('submit', '#updateJokeItemProbabilityForm', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '{{ route("admin.joke-items-loots.updateProbability") }}',
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

    let imageSelected = false;
    $(document).on('submit', '#editFakeItemForm', function(e) {
        e.preventDefault();
        let itemId = $(this).find('[name=id]').val();
        let url = '{{ route("admin.joke-items-loots.update", ":id") }}';
        url = url.replace(':id', itemId);
        $.ajax({
            type: "POST",
            url: url,
            data: new FormData(this),
            cache: false,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() { 
                $("#editFakeItemForm").find('input :submit').prop('disabled', true);
            },
            success: function(response){
                if (imageSelected) {
                    $('#thumb-1').attr('src', $('#previewSrc').attr('value'));
                }
                $('#itemName').html(response.item.name);
                toastr.success(response.message);
                $('#editFakeItemModal').modal('hide');
                $('#editFakeItemForm')[0].reset();
            },
            error: function(jqXHR, exception) {
                toastr.error(errHandling(jqXHR, exception));
            },
            complete: function() { 
                $("#editFakeItemForm").find('input :submit').prop('disabled', false);
            },
        });
    });


    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#previewSrc').attr('value', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#editFakeItemForm [name='image']").change(function() {
        if (this.files.length) {
            readURL(this);
            imageSelected = true;
        }else{
            imageSelected = false;
        }
    });
</script>
@endsection