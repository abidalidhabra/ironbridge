@section('title','Ironbridge1779 | Agent Levels')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Nodes | Agent Levels</h3>
            </div>
            @if(auth()->user()->hasPermissionTo('Add Nodes / Agent Levels'))
                <div class="col-md-6 text-right modalbuttonadd">
                    <button type="button" class="btn btn-info btn-md" data-toggle="modal" data-target="#editNodesAgentLevels">Add</button>
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
                    <th>Special Ability</th>
                    <!-- <th>Power Nodes</th> -->
                    <!-- <th>Bonus Nodes</th> -->
                    <th>Agent Level</th>
                    @if(auth()->user()->hasPermissionTo('Edit Nodes / Agent Levels') || auth()->user()->hasPermissionTo('Delete Nodes / Agent Levels'))
                    <th>Action</th>
                    @endif
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="container">  
    <div class="addnewsmodal_box">
        <div class="modal fade" id="editNodesAgentLevels" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add Nodes | Agent Levels</h4>       
                    </div>
                    <form method="post" id="addNodesAgentForm">
                        @csrf
                        <div class="modal-body">
                            <div class="modalbodysetbox">
                                <div class="">
                                    <div class="form-group">
                                        <label class="control-label">Agent Level:</label>
                                        <select name="agent_level" class="form-control">
                                            <option>Select Agent Level</option>
                                            @forelse($agent_complementary as $agent)
                                                <option value="{{ $agent->agent_level }}">{{ $agent->agent_level }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                </div>      
                                <div class="">
                                    <div class="form-group">
                                        <label class="control-label">Special Ability:</label>
                                        <select name="nodes[]" class="form-control nodes"  multiple="multiple" style="width: 100%;">
                                            <option value="mg_challenge">Mini-game Challenge Nodes</option>
                                            <option value="power">Power Nodes</option>
                                            <option value="bonus">Bonus Nodes</option>
                                        </select>
                                    </div>
                                    <div class="form-group power_box" style="display: none;">
                                        <label class="control-label">Power Value:</label>
                                        <input type="number" name="power" class="form-control">
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
        <div class="modal fade" id="editModel" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Nodes | Agent Levels</h4>       
                    </div>
                    <form method="post" id="editAgentForm">
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('.nodes').select2();

    var table = $('#relics').DataTable({
                    pageLength: 10,
                    processing: true,
                    responsive: true,
                    serverSide: true,
                    order: [],
                    lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                    ajax: {
                        type: "get",
                        url: "{{ route('admin.nodes-agent-levels-list') }}",
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
                        { data:'special_ability',name:'special_ability' },
                        // { data:'power',name:'power' },
                        // { data:'bonus',name:'bonus' },
                        { data:'agent_level',name:'agent_level'},
                        @if(auth()->user()->hasPermissionTo('Edit Nodes / Agent Levels') || auth()->user()->hasPermissionTo('Delete Nodes / Agent Levels'))
                        { data:'action',name:'action' },
                        @endif
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [0,2],
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

     $('#addNodesAgentForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '{{ route("admin.nodes-agent-levels.store") }}',
            data: new FormData(this),
            processData:false,
            cache:false,
            contentType: false,
            success: function(response)
            {
                if (response.status == true) {
                    toastr.success(response.message);
                    document.getElementById("addNodesAgentForm").reset();
                    $(".nodes").select2({
                        allowClear: true
                    });
                    $('.power_box').hide();
                    $('#editNodesAgentLevels').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.warning(response.message);
                }
            }
        });
    });


     /*EDIT MODEL*/
    $(document).on('click', '.edit_agent', function(){
        var id = $(this).data('id');
        var url ='{{ route("admin.nodes-agent-levels.edit",':id') }}';
        url = url.replace(':id',id);
        $.ajax({
            type: "GET",
            url: url,
            beforeSend: function() {
                $('body').css('opacity','0.5');
            },
            success: function(response)
            {
                $('body').css('opacity','1');
                $('#editModel').modal('show');
                $('#editAgentForm').html(response);
                 $('.nodes').select2();
           }
       });
    });

    $(document).on('change','[name="nodes[]"]',function(){
        var array = $(this).val();
        $('.power_box').hide();
        if(jQuery.inArray( "power", array) !== -1){
            $('.power_box').show();
        }
    });

    /* EDIT */
    $('#editAgentForm').submit(function(e) {
        e.preventDefault();
        var id = $('#agent_level_id').val();
        $.ajax({
            type: "POST",
            url: '{{ route("admin.nodes-agent-levels.update","/") }}/'+id,
            data: new FormData(this),
            processData:false,
            cache:false,
            contentType: false,
            success: function(response)
            {
                if (response.status == true) {
                    toastr.success(response.message);
                    $('input[name="complexity"] , input[name="agent_level"]').val('');
                    $('#editModel').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.warning(response.message);
                }
            }
        });
    });
    /* END EDIT */
</script>
@endsection