@section('title','Ironbridge1779 | NEWS')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">      
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Complexity Target</h3>
            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr. No.</th>
                    <th>Complexity</th>
                    <th>Game Name</th>
                    <th>Target</th>
                    <th>Action</th>
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
                        <h4 class="modal-title">Edit Complexity Target</h4>       
                    </div>
                    <form method="post" id="editComplexityForm">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="id">
                            <div class="modalbodysetbox">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" name="complexity" placeholder="Enter complexity" disabled="">
                                    </div>
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
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
                order: [[1, 'desc']],
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
                    { data:'action',name:'action' },
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