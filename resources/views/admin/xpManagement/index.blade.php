@section('title','Ironbridge1779 | Hunts XP')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>XP rewards</h3>
            </div>
        </div>
    </div>
    
    <br><br><br>
    <div>
        <div class="users_datatablebox">
            <div class="row">
                <div class="col-md-6">
                    <h3>Random Hunt Nodes</h3>
                </div>
            </div>
        </div>
        <div class="customdatatable_box">
            <table class="table table-striped table-hover datatables" style="width: 100%;" id="relics">
                <thead>
                    <tr>
                        <th>Sr.</th>
                        <th>Name</th>
                        <th>XP</th>
                        <th>Complexity</th>
                        <th>Created at (UTC)</th>
                        @if(auth()->user()->hasPermissionTo('Edit Hunts XP'))
                        <th>Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <br><br><br>
    <div class="row">
            <form method="post"id="updateDistanceXpForm">
                @csrf
                <div class="col-md-4">
                    <h4>Chest Unlock Reward <span data-toggle="tooltip" title="" data-original-title="Fix XP to provide on chest opening">?</span></h4>
                    <div class="form-group">
                        <input type="number" name="chest_xp" class="form-control" placeholder="Enter the chest XP" value="{{ $hunt_statistic->chest_xp ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <h4>MG Nodes <span data-toggle="tooltip" title="" data-original-title="Fix XP to provide on minigame challenge node completion">?</span></h4>
                    <div class="form-group">
                        <input type="number" name="mgc_xp" class="form-control" placeholder="Enter the MGC XP" value="{{ $hunt_statistic->mgc_xp ?? '' }}">
                    </div>
                </div>
                <!-- <div class="col-md-4">
                    <h4>Relic Hunt Nodes <span data-toggle="tooltip" title="" data-original-title="Fix XP to provide on Relic opening">?</span></h4>
                    <div class="form-group">
                        </label>
                        <input type="number" name="relic_xp" class="form-control" placeholder="Enter the relic XP" value="{{ $hunt_statistic->relic_xp ?? '' }}">
                    </div>
                </div> -->
                <div class="form-group">
                    <button type="submit" class="btn btn-success" style="margin-top: 38px;">Save</button>
                </div>
            </form>
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
                        url: "{{ route('admin.getXpManagementList') }}",
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
                        { data:'xp',name:'xp'},
                        { data:'complexity',name:'complexity'},
                        { data:'created_at',name:'created_at' },
                        @if(auth()->user()->hasPermissionTo('Edit Hunts XP'))
                        { data:'action',name:'action' },
                        @endif
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [0,4],
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

    $(document).on('submit', '#updateDistanceXpForm', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            data: $(this).serialize(),
            url: '{{ route('admin.xpManagement.updateDistanceXp') }}',
            dataType: 'json',
            success: function(response)
            {
                if (response.status == true) {
                    toastr.success(response.message);
                } else {
                    toastr.warning(response.message);
                }
            }
        });
    });
</script>    
</script>
@endsection