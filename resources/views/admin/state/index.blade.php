@section('title','Ironbridge1779 | States')
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
                <h3>States</h3>
            </div>
         
            <div class="col-md-6 text-right modalbuttonadd">
                <button type="button" class="btn btn-info btn-md" data-toggle="modal" data-target="#addState">Add State</button>
            </div>
         
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr.</th>
                    <th>State Name</th>
                    <th>Country</th>
                    <th width="5%">Action</th>
                    
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="container">  
    <div class="addnewsmodal_box">
        <div class="modal fade" id="addState" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add State</h4>       
                    </div>
                    <form method="post" id="addStateForm">
                        @csrf
                        <div class="modal-body">
                            <div class="modalbodysetbox">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <select name="country_id" class="form-control">
                                          <option value="">Select Contry</option>
                                          @foreach($country as $cnt)
                                          <option value="{{ $cnt->id }}">{{ $cnt->name }}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                </div>      
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" class="" name="name" placeholder="State Name" autocomplete="off">
                                        
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
        <div class="modal fade" id="editState" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit State</h4>       
                    </div>
                    <form method="post" id="editStateForm">
                        @method('PATCH')
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="state_id">
                            <div class="modalbodysetbox">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <select name="country_id" class="form-control">
                                          <option value="">Select Contry</option>
                                          @foreach($country as $cnt)
                                          <option value="{{ $cnt->id }}">{{ $cnt->name }}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                </div>             
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <input type="text" class="" name="name" placeholder="State Name" autocomplete="off">
                                        
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
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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
                    url: "{{ route('admin.getStateList') }}",
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
                { data:'name',name:'name'},
                { data:country_name,name:'country_name' },
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



            //ADD NEWS
            $('#addStateForm').submit(function(e) {
                e.preventDefault();
            })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    country_id: { required: true },
                    name: { required: true },
                    
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.state.store") }}',
                        data: formData,
                      
                        processData:false,
                        cache:false,
                        contentType: false,
                        success: function(response)
                        {
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('input[name="name"] ,select[name="country_id"]').val('');
                                $('.preview-content').attr('src',' ')
                                $('#addState').modal('hide');
                                table.ajax.reload();
                            } else {
                                toastr.warning(response.message);
                            }
                        }
                    });
                }
            });
           function country_name(data){
                     if(data.country){
                    return data.country.name;    
                    }
                    else{
                        return '';
                    }
                 }

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
                            url: '{{ route("admin.state.destroy","/") }}/'+id,
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
                $("#editState input[name='state_id']").val($(this).data('id'));
                $("#editState input[name='name']").val($(this).data('cityname'));
                $("#editState select[name='country_id']").val($(this).data('country'));
                
                
                $('#editState').modal('show');
            });


            //EDIT NEWS
            $('#editStateForm').submit(function(e) {
                e.preventDefault();
            })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    name: { required: true },
                    country_id: { required: true },
                    
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    var id = $('input[name="state_id"]').val();
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.state.update","") }}/'+id,
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
                                $('input[name="name"] , select[name="country_id"]').val('');
                                $('.preview-content').attr('src',' ')
                                $('#editState').modal('hide');
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