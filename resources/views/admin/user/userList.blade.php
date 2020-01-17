@section('title','Ironbridge1779 | User')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="users_datatablebox userstextset">
            <h3>Users</h3>
        </div>
        <div class="customdatatable_box">
            <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
                <thead>
                    <tr>
                        <th>Sr.</th>
                        <th>Sign Up Date</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Gold</th>
                        <th>Key(s)</th>
                        <th>Device</th>
                        <!-- <th>Date of birth</th> -->
                        @if(auth()->user()->hasPermissionTo('Add Users'))
                        <th>Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- ADD GOLD MODEL -->
    <!-- Modal -->
    <div class="modal fade" id="addgoldModel" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Gold</h4>
                </div>
                <form method="post" id="addGoldForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Add Gold</label>
                            <input type="hidden" name="id" id="id">
                            <input type="number" class="form-control" placeholder="Enter the gold" name='gold' id="gold">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ADD SKELETON KEY -->
    <div class="modal fade" id="addskeletonModel" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Skeleton Keys</h4>
                </div>
                <form method="post" id="addSekelotonForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Add Skeleton Keys</label>
                            <input type="hidden" name="user_id" id="user_id">
                            <input type="number" class="form-control" placeholder="Enter the skeleton keys" name='skeleton_key' id="skeleton_key">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
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
                order:[],
                lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                ajax: {
                    type: "get",
                    url: "{{ route('admin.getUsers') }}",
                    data: function ( d ) {
                        d._token = "{{ csrf_token() }}";
                    },
                    complete:function(){
                        if( $('[data-toggle="tooltip"]').length > 0 )
                            $('[data-toggle="tooltip"]').tooltip();
                    }
                },
                columns:[
                    { data:'DT_RowIndex',name:'_id'},
                    { data:'created_at',name:'created_at'},
                    { data:'name',name:'name' },
                    { data:'email',name:'email' },
                    { data:'username',name:'username' },
                    { data:'gold_balance',name:'gold_balance'},
                    { data:'skeleton_keys',name:'skeleton_keys'},
                    { data:'device',name:'device'},
                    // { data:'dob',name:'dob'},
                    @if(auth()->user()->hasPermissionTo('Add Users'))
                    { className : 'details-control', defaultContent : '', data    : null,orderable : false},
                    @endif
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0],
                    }
                ],

            });

            function format (data) {
                var reset_asscount = '';
                @if(auth()->user()->hasPermissionTo('Reset Users'))
                    reset_asscount = '<div class="view_td_set"><a href="javascript:void(0)" class="btn btn-info" data-id="'+data._id+'" data-action="reset" data-placement="left" title="Delete" > Reset An Account</a> </div>';
                @endif
                return '<div class="details-container">'+
                    '<table cellpadding="2" cellspacing="0" border="0" class="details-table">'+
                        '<tr>'+'<td class="title">\
                                <div class="view_td_set"><button type="button" class="btn btn-info" data-id="'+data._id+'" data-action="btnAdd" data-toggle="modal"> Add Gold</button> </div>\
                                <div class="view_td_set"><a href="javascript:void(0)" class="btn btn-info" data-id="'+data._id+'" data-action="skeleton"> Add Skeleton Key</a> </div>'+reset_asscount+
                            '</td>'+
                    '</tr>'+
                    '</table>'+
                '</div>';
            };


            $('#dataTable tbody').on('click', 'td.details-control', function () {
                var tr  = $(this).closest('tr'),
                row = table.row(tr);

                if (row.child.isShown()) {
                    tr.next('tr').removeClass('details-row');
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    row.child(format(row.data())).show();
                    tr.next('tr').addClass('details-row');
                    tr.addClass('shown');

                    $("a[data-action='reset']").confirmation({
                        container:"body",
                        btnOkLabel:"Reset",
                        btnOkClass:"btn btn-sm btn-success",
                        btnCancelClass:"btn btn-sm btn-danger",
                        onConfirm:function(event, element) {
                            var id = element.attr('data-id');
                            let url = "{{ route('admin.user.reset',':userId') }}";
                            url = url.replace(":userId", id);
                            $.ajax({
                                type: "POST",
                                url: url,
                                data: $(this).serialize(),
                                success: function(response) {
                                    toastr.success(response.message);
                                },
                                error: function(xhr, exception) {
                                    let error = JSON.parse(xhr.responseText);
                                    toastr.error(error.message);
                                }
                            });
                        }
                    }); 
                }
            });
            

            //ADD GOLD MODEL SHOW
            $("table").delegate("button[data-action='btnAdd']", "click", function() {
                var id = $(this).attr('data-id');
                $('#id').val(id);
                $('#addgoldModel').modal('show');
            });

            $("table").delegate('a[data-action="skeleton"]', "click", function() {
                var id = $(this).attr('data-id');
                $('#user_id').val(id);
                $('#addskeletonModel').modal('show');
            }); 
            
            


            //ADD GOLD 
            $('#addGoldForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: '{{ route("admin.addGold") }}',
                    data: $(this).serialize(),
                    success: function(response)
                    {
                        if (response.status == true) {
                            table.ajax.reload();
                            toastr.success(response.message);
                            $('#addgoldModel').modal('hide');
                            $('#gold , #id').val('');
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            });

            //ADD Skeleton
            $('#addSekelotonForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: '{{ route("admin.addSkeletonKey") }}',
                    data: $(this).serialize(),
                    success: function(response)
                    {
                        if (response.status == true) {
                            table.ajax.reload();
                            toastr.success(response.message);
                            $('#addskeletonModel').modal('hide');
                            $('#skeleton_key , #user_id').val('');
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            });

        });
    </script>
@endsection