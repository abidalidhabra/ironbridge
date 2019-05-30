@section('title','ironbridge1779 | NEWS')
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
                <h3>News</h3>
            </div>
            <div class="col-md-6 text-right modalbuttonadd">
                <button type="button" class="btn btn-info btn-md" data-toggle="modal" data-target="#addNews">Add News</button>
            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th width="10%">News Date</th>
                    <th width="5%">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="container">  
    <div class="addnewsmodal_box">
        <div class="modal fade" id="addNews" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add News</h4>       
                    </div>
                    <form method="post" id="addNewsForm">
                        @csrf
                        <div class="modal-body">
                            <div class="modalbodysetbox">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" name="subject" placeholder="subject">
                                    </div>
                                </div>             
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">                  
                                        <textarea class="form-control rounded-0" name="description" rows="3" placeholder="Description"></textarea>
                                    </div>
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="input-group date" data-provide="datepicker">
                                        <input type="text" class="" name="valid_till" placeholder="Date" autocomplete="off">
                                        <div class="input-group-addon">                   
                                        </div>
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
        <div class="modal fade" id="editNews" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit News</h4>       
                    </div>
                    <form method="post" id="editNewsForm">
                        @method('PATCH')
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="news_id">
                            <div class="modalbodysetbox">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" name="subject" placeholder="subject">
                                    </div>
                                </div>             
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">                  
                                        <textarea class="form-control rounded-0" name="description" rows="3" placeholder="Description"></textarea>
                                    </div>
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="input-group date" data-provide="datepicker">
                                        <input type="text" class="" name="valid_till" placeholder="Date" autocomplete="off">
                                        <div class="input-group-addon">                   
                                        </div>
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
                    url: "{{ route('admin.getNewsList') }}",
                    data: function ( d ) {
                        d._token = "{{ csrf_token() }}";
                    },
                    complete:function(){
                        afterfunction();
                        if( $('[data-toggle="tooltip"]').length > 0 )
                            $('[data-toggle="tooltip"]').tooltip();
                    }
                },
                columns:[
                    { data:'DT_RowIndex',name:'_id' },
                    { data:'subject',name:'created_at'},
                    { data:'description',name:'description' },
                    { data:'valid_till',name:'valid_till' },
                    { data:'action',name:'action' },
                ],

            });
           
            //ADD NEWS
            $('#addNewsForm').submit(function(e) {
                    e.preventDefault();
                })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    subject: { required: true },
                    description: { required: true },
                    valid_till: { required: true },
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.news.store") }}',
                        data: formData,
                        processData:false,
                        cache:false,
                        contentType: false,
                        success: function(response)
                        {
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('input[name="subject"] , textarea[name="description"] , input[name="valid_till"]').val('');
                                $('#addNews').modal('hide');
                                table.ajax.reload();
                            } else {
                                toastr.warning(response.message);
                            }
                        }
                    });
                }
            });


            function afterfunction(){

                //DELETE ACCOUNT
                $("a[data-action='delete']").confirmation({
                    container:"body",
                    btnOkClass:"btn btn-sm btn-success",
                    btnCancelClass:"btn btn-sm btn-danger",
                    onConfirm:function(event, element) {
                        var id = element.attr('data-id');
                        $.ajax({
                            type: "delete",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '{{ route("admin.news.destroy","/") }}/'+id,
                            data: {id : id},
                            success: function(response)
                            {
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

            /*EDIT MODEL*/
            $(document).on('click', '.edit_company', function(){
                $("#editNews input[name='subject']").val($(this).data('subject'));
                $("#editNews textarea[name='description']").val($(this).data('description'));
                $("#editNews input[name='valid_till']").val($(this).data('valid_till'));
                $("#editNews input[name='news_id']").val($(this).data('id'));
                $('#editNews').modal('show');
            });


            //EDIT NEWS
            $('#editNewsForm').submit(function(e) {
                    e.preventDefault();
                })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    subject: { required: true },
                    description: { required: true },
                    valid_till: { required: true },
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    var id = $('input[name="news_id"]').val();
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.news.update","") }}/'+id,
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
                                $('input[name="subject"] , textarea[name="description"] , input[name="valid_till"]').val('');
                                $('#editNews').modal('hide');
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