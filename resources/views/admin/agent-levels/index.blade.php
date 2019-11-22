@section('title','Ironbridge1779 | Hunt Agent Levels')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Agent Levels</h3>
            </div>
            @if(auth()->user()->hasPermissionTo('Add Agent Levels'))
                <div class="col-md-6 text-right modalbuttonadd">
                    <a href="#addAgentLevelModal" class="btn btn-info btn-md" data-toggle="modal">Add</a>
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
                    <th>XP Points</th>
                    <th>Agent Levels</th>
                    @if(auth()->user()->hasPermissionTo('Edit Agent Levels') || auth()->user()->hasPermissionTo('Delete Agent Levels'))
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
        <div class="modal fade" id="addAgentLevelModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add Agent Levels</h4>       
                    </div>
                    <form method="post" id="addAgentLevelForm">
                        @csrf
                        <div class="modal-body">
                            <div class="modalbodysetbox">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" name="agent_level" placeholder="Agent Level">
                                    </div>
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" name="xps" placeholder="XP Points">
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
        <div class="modal fade" id="editAgentLevelModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Agent Levels</h4>       
                    </div>
                    <form method="post" id="editAgentLevelForm">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <div class="modalbodysetbox">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" name="agent_level" placeholder="Agent Level">
                                        <input type="hidden" name="_id">
                                    </div>
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" name="xps" placeholder="XP Points">
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
	
	$(document).on('click', '.editAgentLevel', function() {
		$.ajax({
			type: "GET",
			url: $(this).attr('data-edit-path'),
			dataType: 'json',
			success: function(response){
				if (response.status == true) {
					$('#editAgentLevelForm input[name="agent_level"]').val(response.agent_complementary.agent_level);
					$('#editAgentLevelForm input[name="xps"]').val(response.agent_complementary.xps);
					$('#editAgentLevelForm input[name="_id"]').val(response.agent_complementary._id);
					$('#editAgentLevelModal').modal('show');
				} else {
					toastr.warning(response.message);
				}
			},
			error: function(jqXHR, exception) {
				let errors = JSON.parse(jqXHR.responseText).errors;
				toastr.warning(errors[Object.keys(errors)[0]]);
			}
		});
	});

	$('#editAgentLevelForm').on('submit', function(e) {
		e.preventDefault();
		let url = "{{ route('admin.agent-levels.update', ':agentComplementaryId') }}";
		url = url.replace(':agentComplementaryId', $('#editAgentLevelForm input[name="_id"]').val());
		$.ajax({
			type: "POST",
			url: url,
			data: $(this).serialize(),
			dataType: 'json',
			success: function(response){
				if (response.status == true) {
					toastr.success(response.message);
					$('#editAgentLevelModal').modal('hide');
					table.ajax.reload();
				} else {
					toastr.warning(response.message);
				}
			},
			error: function(jqXHR, exception) {
				let errors = JSON.parse(jqXHR.responseText).errors;
				toastr.warning(errors[Object.keys(errors)[0]]);
			}
		});
	});

	$('#addAgentLevelForm').on('submit', function(e) {
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "{{ route('admin.agent-levels.store') }}",
			data: $(this).serialize(),
			dataType: 'json',
			success: function(response){
				if (response.status == true) {
					toastr.success(response.message);
					$('#addAgentLevelModal').modal('hide');
					table.ajax.reload();
				} else {
					toastr.warning(response.message);
				}
			},
			error: function(jqXHR, exception) {
				let errors = JSON.parse(jqXHR.responseText).errors;
				toastr.warning(errors[Object.keys(errors)[0]]);
			}
		});
	});

    var table = $('#relics').DataTable({
                    pageLength: 10,
                    processing: true,
                    responsive: true,
                    serverSide: true,
                    order: [],
                    lengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
                    ajax: {
                        type: "get",
                        url: "{{ route('admin.agent-levels.list') }}",
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
                        { data:'xps',name:'xps'},
                        { data:'agent_level',name:'agent_level'},
                        @if(auth()->user()->hasPermissionTo('Edit Agent Levels') || auth()->user()->hasPermissionTo('Delete Agent Levels'))
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
</script>
@endsection