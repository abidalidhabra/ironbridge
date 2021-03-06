@section('title','Ironbridge1779 | Games')
@extends('admin.layouts.admin-app')
@section('styles')
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">      
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Games</h3>
            </div>
            @if(auth()->user()->hasPermissionTo('Add Games'))
            <div class="col-md-6 text-right modalbuttonadd">
                <button type="button" class="btn btn-info btn-md" data-toggle="modal" data-target="#addNews">Add Game</button>
            </div>
            @endif
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr.</th>
                    <th>Identifier</th>
                    <th>Name</th>
                    <th>Status</th>
                    @if(auth()->user()->hasPermissionTo('Edit Games'))
                    <th width="5%">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="container">  
    <div class="addnewsmodal_box">
        <div class="modal fade" id="addNews" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add Game</h4>       
                    </div>
                    <form method="post" id="addGameForm">
                        @csrf
                        <div class="modal-body">
                            <div class="modalbodysetbox">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" name="identifier" placeholder="identifier">
                                    </div>
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" name="name" placeholder="name">
                                    </div>
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <select name="status">
                                            <option value="">Please select status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
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
        <div class="modal fade" id="editGame" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Game</h4>       
                    </div>
                    <form method="post" id="editGameForm">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="game_id">
                            <div class="modalbodysetbox">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" name="identifier" placeholder="identifier">
                                    </div>
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" name="name" placeholder="name">
                                    </div>
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <select name="status">
                                            <option value="">Please select status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
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
<script type="text/javascript">
    $(document).ready(function() {
            //GET USER LIST
            var table = $('#dataTable').DataTable({
                pageLength: 10,
                processing: true,
                responsive: true,
                serverSide: true,
                order: [],
                lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                ajax: {
                    type: "get",
                    url: "{{ route('admin.getGameList') }}",
                    data: function ( d ) {
                        d._token = "{{ csrf_token() }}";
                    },
                    complete:function(){
                        afterfunction();
                        if( $('[data-toggle="tooltip"]').length > 0 )
                            $('[data-toggle="tooltip"]').tooltip();
                    }
                },
                columns:[
                { data:'DT_RowIndex',name:'_id' },
                { data:'identifier',name:'identifier'},
                { data:'name',name:'name' },
                { data:'status',name:'status' },
                @if(auth()->user()->hasPermissionTo('Edit Games'))
                { data:'action',name:'action' },
                @endif
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0,4],
                    }
                ],

            });

            //ADD NEWS
            $('#addGameForm').submit(function(e) {
                e.preventDefault();
            })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    identifier: { required: true },
                    name: { required: true },
                    status: { required: true },
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.addgame") }}',
                        data: formData,
                        processData:false,
                        cache:false,
                        contentType: false,
                        success: function(response)
                        {
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('input[name="subject"] , textarea[name="description"] , input[name="valid_till"]').val('');
                                $('#addNews').modal('hide');
                                table.ajax.reload();
                            } else {
                                toastr.warning(response.message);
                            }
                        }
                    });
                }
            });


            function afterfunction(){

                //DELETE ACCOUNT
                $("a[data-action='delete']").confirmation({
                    container:"body",
                    btnOkClass:"btn btn-sm btn-success",
                    btnCancelClass:"btn btn-sm btn-danger",
                    onConfirm:function(event, element) {
                        var id = element.attr('data-id');
                        $.ajax({
                            type: "delete",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '{{ route("admin.deleteGame","/") }}/'+id,
                            data: {id : id},
                            success: function(response)
                            {
                                if (response.status == true) {
                                    toastr.success(response.message);
                                    table.ajax.reload();
                                } else {
                                    toastr.warning(response.message);
                                }
                            }
                        });
                    }
                });  
                
            }

            /*EDIT MODEL*/
            $(document).on('click', '.edit_game', function(){
                $("#editGame input[name='identifier']").val($(this).data('identifier'));
                $("#editGame input[name='name']").val($(this).data('name'));
                $("#editGame input[name='game_id']").val($(this).data('id'));
                $('#editGame option[value="active"] , #editGame option[value="inactive"]').removeAttr('selected');
                if ($(this).data('status') == true) {
                    $("#editGame select[name='status']").find('option[value="active"]').prop('selected',true);
                } else if($(this).data('status') == false){
                    $("#editGame select[name='status']").find('option[value="inactive"]').prop('selected',true);
                }
                $('#editGame').modal('show');
            });


            //EDIT NEWS
            $('#editGameForm').submit(function(e) {
                e.preventDefault();
            })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    identifier: { required: true },
                    name: { required: true },
                    status: { required: true },
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.editGame") }}',
                        data: formData,
                        processData:false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        cache:false,
                        contentType: false,
                        beforeSend: function() {
                            $('#editGameForm [type=submit]').html('<i class="fa fa-spinner fa-spin"></i> Save');
                        },
                        success: function(response)
                        {
                            $('#editGameForm [type=submit]').html('Save');
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('input[name="identifier"] , input[name="name"] , select[name="status"]').val('');
                                $('#editGame').modal('hide');
                                table.ajax.reload();
                            } else {
                                toastr.warning(response.message);
                            }
                        }
                    });
                }
            });
        });
    </script>
    @endsection