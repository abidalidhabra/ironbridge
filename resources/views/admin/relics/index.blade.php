@section('title','Ironbridge1779 | Relics')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Relics</h3>
            </div>
            @if(auth()->user()->hasPermissionTo('Add Relics'))
                <div class="col-md-6 text-right modalbuttonadd">
                    <a href="{{ route('admin.relics.create') }}" class="btn btn-info btn-md">Add Relic</a>
                </div>
            @endif
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="relics">
            <thead>
                <tr>
                    <th>Sr.</th>
                    <th>Name</th>
                    <th>Number</th>
                    <th>Image</th>
                    <th>TH Complexity</th>
                    <th>Relic Map Pieces</th>
                    <!-- <th>Created at (UTC)</th> -->
                    <th>Status</th>
                     @if(auth()->user()->hasPermissionTo('Edit Relics') || auth()->user()->hasPermissionTo('Delete Relics'))
                    <th>Action</th>
                    @endif
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var table = $('#relics').DataTable({
                    pageLength: 10,
                    processing: true,
                    responsive: true,
                    serverSide: true,
                    order: [],
                    lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                    ajax: {
                        type: "get",
                        url: "{{ route('admin.relics.list') }}",
                        data: function ( d ) {
                            d._token = "{{ csrf_token() }}";
                        },
                        complete:function(){
                            initializeDeletePopup();
                            if( $('[data-toggle="tooltip"]').length > 0 )
                                $('[data-toggle="tooltip"]').tooltip();
                        }
                    },
                    columns:[
                        { data:'DT_RowIndex',name:'_id' },
                        { data:'name',name:'name'},
                        { data:'number',name:'number'},
                        { data:'icon',name:'icon'},
                        { data:'complexity',name:'complexity'},
                        { data:'pieces',name:'pieces'},
                        { data:'active',name:'active' },
                        @if(auth()->user()->hasPermissionTo('Edit Relics') || auth()->user()->hasPermissionTo('Delete Relics'))
                         { data:'action',name:'action' },
                        @endif
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [0,3,7],
                    }],
                });

    function initializeDeletePopup() {
        $("a[data-action='delete']").confirmation({
            container:"body",
            btnOkClass:"btn btn-sm btn-success",
            btnCancelClass:"btn btn-sm btn-danger",
            onConfirm:function(event, element) {
                event.preventDefault();
                $.ajax({
                    type: "DELETE",
                    url: element.attr('href'),
                    success: function(response){
                        if (response.status == true) {
                            toastr.success(response.message);
                            table.ajax.reload();
                        } else {
                            toastr.warning(response.message);
                        }
                    },
                    error: function(xhr, exception) {
                        let error = JSON.parse(xhr.responseText);
                        toastr.error(error.message);
                    }
                });
            }
        }); 
    }
</script>
@endsection