@section('title','Ironbridge1779 | User')
@extends('admin.layouts.admin-app')
@section('styles')
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Users</h3>
            </div>
            <div class="col-md-6 text-right modalbuttonadd">
                <button type="button" class="btn btn-info btn-md" data-toggle="modal" data-target="#addAdmin">Add User</button>
            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th>Sr</th>
                    <th>Email</th>
                    <th>Created Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="container">  
    <div class="addnewsmodal_box">
        <div class="modal fade bd-example-modal-lg" id="addAdmin" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add User</h4>       
                    </div>
                    <form method="post" id="addAdminForm">
                        @csrf
                        <div class="modal-body">
                            <div class="modalbodysetbox">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" name="email" placeholder="email">
                                    </div>
                                </div>
                                <?php $module=""; ?>             
                                @foreach($permissions as $permission)
                                @if($module != $permission->module)<div class="clearfix"></div> <h5>{{$permission->module}}</h5> <hr><div class="clearfix"></div>@endif
                                <div class="col-md-4 ml-auto">
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="permissions[]" id="{{$permission->id}}" value="{{$permission->name}}">{{$permission->name}}</label>
                                    </div>
                                </div>
                                <?php $module = $permission->module; ?>             
                                @endforeach
                                <label for="permissions[]" class="error"></label>
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
        <div class="modal fade bd-example-modal-lg" id="editAdmin" role="dialog">
            <div class="modal-dialog  modal-lg">
                <!-- Modal content>-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit User</h4>       
                    </div>
                    <form method="post" id="editAdminForm">
                        @csrf
                        @method('put')
                        <div class="modal-body">
                            <div class="modalbodysetbox" id="adminEditContent">
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
<!-- <script type="text/javascript" src="{{ asset('js/toastr.min.js') }}"></script> -->
<script type="text/javascript">
    $(document).ready(function() {
            //GET USER LIST
            var table = $('#dataTable').DataTable({
                pageLength: 50,
                processing: true,
                responsive: true,
                serverSide: true,
                order: [[1, 'desc']],
                lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                ajax: {
                    type: "get",
                    url: "{{ route('admin.getAdminsList') }}",
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
                { data:'email',name:'email' },
                { data:'created_at',name:'created_at'},
                { data:'action',name:'action'},
                ],

            });
            

            $('#addAdminForm').submit(function(e) {
                e.preventDefault();
            })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    email: { required: true },
                    'permissions[]': { required: true },
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.adminManagement.store") }}',
                        data: formData,
                        processData:false,
                        cache:false,
                        contentType: false,
                        success: function(response)
                        {
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('input[name="email"] , input[name="permissions[]"]').val('');
                                $('#addAdmin').modal('hide');
                                table.ajax.reload();
                            } else {
                                toastr.warning(response.message);
                            }
                        }
                    });
                }
            });

            $(document).on('click', '.edit_admin', function(){
                $('#editAdmin').modal('show');
                var id = $(this).data('id');
                var url ='{{ route("admin.adminManagement.edit",':id') }}';
                url = url.replace(':id',id);
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(response)
                    {
                       $('#adminEditContent').html(response);
                   }
               });
            });
     //EDIT NEWS
     $('#editAdminForm').submit(function(e) {
        e.preventDefault();
    })
     .validate({
        focusInvalid: false, 
        ignore: "",
        rules: {
            email: { required: true },
            'permissions[]': { required: true },
        },
        submitHandler: function (form) {
            var formData = new FormData(form);
            var id  = $('#admin_id').val();
            var url ='{{ route("admin.adminManagement.update",':id') }}';
            url = url.replace(':id',id);

            $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData:false,
                cache:false,
                contentType: false,
                success: function(response)
                {
                    if (response.status == true) {
                        toastr.success(response.message);
                        $('input[name="email"] , input[name="permissions[]"]').val('');
                        $('#editAdmin').modal('hide');
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
                        var url ='{{ route("admin.adminManagement.destroy",':id') }}';
                        url = url.replace(':id',id);
                        $.ajax({
                            type: "DELETE",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: url,
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
        });
    </script>
    @endsection