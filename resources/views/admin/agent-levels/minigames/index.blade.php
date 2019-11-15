@section('title','Ironbridge1779 | Agent Levels')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Minigames Agent Level</h3>
            </div>
            @if(auth()->user()->hasPermissionTo('Add Agent Levels'))
                <div class="col-md-6 text-right modalbuttonadd">
                    <!-- <button type="button" class="btn btn-info btn-md" data-toggle="modal" data-target="#addMinigamesAgentLevels">Add</button> -->
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
                    <th>Minigames</th>
                    <th>Agent Level</th>
                    <!-- <th>XP Points</th> -->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="container">  
    <div class="addnewsmodal_box">
        <div class="modal fade" id="addMinigamesAgentLevels" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add Minigames Agent Level</h4>       
                    </div>
                    <form method="post" id="addMinigamesAgentForm">
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
                                        <label class="control-label">Minigames:</label>
                                        <select name="minigames[]" class="form-control minigames" id="" multiple="multiple" style="width: 100%;">
                                            <option value="">Select Minigames</option>
                                            @forelse($games as $game)
                                            <option value="{{ $game->id }}">{{ $game->name }}</option>
                                            @empty
                                            @endforelse
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
        <div class="modal fade" id="editModel" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Minigames Agent Level</h4>       
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
    $('.minigames').select2();

    var table = $('#relics').DataTable({
                    pageLength: 10,
                    processing: true,
                    responsive: true,
                    serverSide: true,
                    order: [],
                    lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                    ajax: {
                        type: "get",
                        url: "{{ route('admin.minigames-agent-levels-list') }}",
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
                        { data:'minigames',name:'minigames' },
                        { data:'agent_level',name:'agent_level'},
                        { data:'action',name:'action' },
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

     $('#addMinigamesAgentForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '{{ route("admin.minigames-agent-levels.store") }}',
            data: new FormData(this),
            processData:false,
            cache:false,
            contentType: false,
            success: function(response)
            {
                if (response.status == true) {
                    toastr.success(response.message);
                    $('input[name="complexity"] , input[name="agent_level"]').val('');
                    $('#addMinigamesAgentLevels').modal('hide');
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
        var url ='{{ route("admin.minigames-agent-levels.edit",':id') }}';
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
                 $('.minigames').select2();
           }
       });
    });

    /* EDIT */
    $('#editAgentForm').submit(function(e) {
        e.preventDefault();
        var id = $('#agent_level_id').val();
        $.ajax({
            type: "POST",
            url: '{{ route("admin.minigames-agent-levels.update","/") }}/'+id,
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