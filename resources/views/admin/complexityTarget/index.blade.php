@section('title','Ironbridge1779 | Chest Targets')
@extends('admin.layouts.admin-app')
@section('styles')
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">      
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Chest Targets</h3>
            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr.</th>
                    <th>Complexity</th>
                    <th>Game Name</th>
                    <th>Target</th>
                    @if(auth()->user()->hasPermissionTo('Edit Complexity Targets'))
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
        <div class="modal fade" id="edit_complexity" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Chest Targets</h4>       
                    </div>
                    <form method="post" id="editComplexityForm">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="id">
                            <div class="modalbodysetbox">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label>Complexity</label>
                                        <input type="text" name="complexity" placeholder="Enter complexity" disabled="">
                                    </div>
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label>Game Name</label>
                                        <select name="game" disabled>
                                            @forelse($games as $game)
                                            <option value="{{ $game->id }}">{{ $game->name }}</option>
                                            @empty
                                            <option value="">No game found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label>Target<small class="target"></small></label>
                                        <input type="text" name="target" placeholder="target">
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
                    url: "{{ route('admin.getComplexityTarget') }}",
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
                { data:'complexity',name:'complexity'},
                { data:'game_name',name:'game_name' },
                { data:'target',name:'target' },
                @if(auth()->user()->hasPermissionTo('Edit Complexity Targets'))
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
            

            /*EDIT MODEL*/
            $(document).on('click', '.edit_complexity', function(){
                $("#edit_complexity input[name='id']").val($(this).data('id'));
                $("#edit_complexity input[name='complexity']").val($(this).data('complexity'));
                $("#edit_complexity input[name='target']").val($(this).data('target'));
                var gameid = $(this).data('game_id');

                $('#edit_complexity select[name="game"] option').removeAttr('selected');
                
                $("#edit_complexity select[name='game']").find('option[value="'+gameid+'"]').attr('selected','selected');
                
                $('#edit_complexity').modal('show');
                
                $('.target').text('');
                if (gameid == '5c188ab5719a1408746c473b') {
                    $('.target').text(' must of [512,1024,2048,4096]');
                }
                if (gameid == '5b0e304b51b2010ec820fb4e') {
                    $('.target').text(' must of [12,35,70,140]');
                }
                if (gameid == '5b0e306951b2010ec820fb4f') {
                    $('.target').text(' must of [4,5,6]');
                }
                if (gameid == '5b0e2ff151b2010ec820fb48') {
                    $('.target').text(' must be between [1 to 81]');
                }

            });


            //EDIT NEWS
            $('#editComplexityForm').submit(function(e) {
                e.preventDefault();
            })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    game: { required: true },
                    target: { required: true,digits: true },
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.editComplexityTarget") }}',
                        data: formData,
                        processData:false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        cache:false,
                        contentType: false,
                        success: function(response)
                        {
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('#edit_complexity').modal('hide');
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