@section('title','Ironbridge1779 | Bonus Treasure Nodes Target')
@extends('admin.layouts.admin-app')
@section('styles')
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">      
    <div class="">
    </div>
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Bonus Treasure Nodes Target</h3>
            </div>
            <!-- <div class="col-md-6 text-right modalbuttonadd">
                <button type="button" class="btn btn-info btn-md" data-toggle="modal" data-target="#addTreasureNodesTargets">Add</button>
            </div> -->
            
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr.</th>
                    <th>Game</th>
                    <th>Score</th>
                    <th width="5%">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="container">  
    <div class="addTreasureNodesTargets">
        <div class="modal fade" id="editTreasureNodesTargets" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit</h4>       
                    </div>
                    <form method="post" id="editTreasureNodesTargetsForm">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="treasure_nodes_targets_id">
                            <div class="modalbodysetbox">
                                <div class="form-group">
                                    <label>Game:</label>
                                    <select class="form-control" name="game_id" disabled="true">
                                        @forelse($games as $game)
                                        <option value="{{ $game->id }}">{{ $game->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Score:</label>
                                    <input type="number" class="form-control" name="score" placeholder="score">
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

    function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
          $('.preview-content').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]);
      }
    }

    $("input[name=image]").change(function() {
      readURL(this);
    });

</script>
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
                    url: "{{ route('admin.getTreasureNodesTargetsList') }}",
                    data: function ( d ) {
                        d._token = "{{ csrf_token() }}";
                    },
                    complete:function(){
                        if( $('[data-toggle="tooltip"]').length > 0 )
                            $('[data-toggle="tooltip"]').tooltip();
                    }
                },
                columns:[
                { data:'DT_RowIndex',name:'_id' },
                { data:'game',name:'game'},
                { data:'score',name:'score'},
                { data:'action',name:'action' },
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0,3],
                        // "bSortable": false
                    }
                ],

            });


            /*EDIT MODEL*/
            $(document).on('click', '.edit_treasureNodesTarge', function(){
                $("#editTreasureNodesTargets input[name='score']").val($(this).data('score'));
                $("#editTreasureNodesTargets input[name='treasure_nodes_targets_id']").val($(this).data('id'));
                // $("#editTreasureNodesTargets select[name='game_id']").val($(this).data('game'));
                $("#editTreasureNodesTargets option[value='"+$(this).data('game')+"']").prop('selected', true);
                $('#editTreasureNodesTargets').modal('show');
            });


            //EDIT NEWS
            $('#editTreasureNodesTargetsForm').submit(function(e) {
                e.preventDefault();
                var id = $('input[name="treasure_nodes_targets_id"]').val();
                $.ajax({
                    type: "POST",
                    url: '{{ route("admin.treasure_nodes_targets.update","/") }}/'+id,
                    data: $('#editTreasureNodesTargetsForm').serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response)
                    {
                        if (response.status == true) {
                            toastr.success(response.message);
                            $('#editTreasureNodesTargets').modal('hide');
                            table.ajax.reload();
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            });
        });
    </script>
    @endsection