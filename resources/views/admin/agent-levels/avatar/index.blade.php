@section('title','Ironbridge1779 | Agent Levels')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Avatar | Agent Levels</h3>
            </div>
            @if(auth()->user()->hasPermissionTo('Add Avatar / Agent Levels'))
                <div class="col-md-6 text-right modalbuttonadd">
                    <a href="{{ route('admin.avatar-agent-levels.create') }}" class="btn btn-info btn-md">Add</a>
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
                    <th>Total Avatar Items</th>
                    <th>Agent Levels</th>
                    @if(auth()->user()->hasPermissionTo('Delete Avatar / Agent Levels') || auth()->user()->hasPermissionTo('Edit Avatar / Agent Levels'))
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
                        url: "{{ route('admin.avatar-agent-levels-list') }}",
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
                        { data:'total_avatar',name:'total_avatar' },
                        { data:'agent_level',name:'agent_level'},
                        @if(auth()->user()->hasPermissionTo('Delete Avatar / Agent Levels') || auth()->user()->hasPermissionTo('Edit Avatar / Agent Levels'))
                        { data:'action',name:'action' },
                        @endif
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [0,3],
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
                    }
                });
            }
        }); 
    }
</script>
@endsection