@section('title','Ironbridge1779 | Country')
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
                <h3>Country</h3>
            </div>
         
            <div class="col-md-6 text-right modalbuttonadd">
                <button type="button" class="btn btn-info btn-md" data-toggle="modal" data-target="#addCountry">Add Country</button>
            </div>
         
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr.</th>
                    <th>Country Name</th>
                    <th>Country Code</th>
                    <th>Currency</th>
                    <th>Currency Full Name</th>
                    <th>Currency Symbol</th>
                    <th>Dialing Code</th>
                    <th width="5%">Action</th>
                    
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="container">  
    <div class="addnewsmodal_box">
        <div class="modal fade" id="addCountry" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add Country</h4>       
                    </div>
                    <form method="post" id="addCountryForm">
                        @csrf
                        <div class="modal-body">
                            <div class="modalbodysetbox">
                                   
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label class="form-label">Country Name</label>
                                        <input type="text" class="" name="name" placeholder="Country Name" autocomplete="off">
                                    </div>
                                </div>       
                                 <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label class="form-label">Country Code</label>
                                        <input type="text" class="" name="code" placeholder="Country Code" autocomplete="off">
                                    </div>
                                </div>       
                                 <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label class="form-label">Country Currency</label>
                                        <input type="text" class="" name="currency" placeholder="Country Currency" autocomplete="off">
                                    </div>
                                </div>       
                                 <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label class="form-label">Currency Full Name</label>
                                        <input type="text" class="" name="currency_full_name" placeholder="Currency Full Name" autocomplete="off">
                                    </div>
                                </div>       
                                 <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                         <label class="form-label">Currency Symbol</label>
                                        <input type="text" class="" name="currency_symbol" placeholder="Currency Symbol" autocomplete="off">
                                    </div>
                                </div>       
                                 <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label class="form-label">Dialing Code</label>
                                        <input type="text" class="" name="dialing_code" placeholder="Dialing Code" autocomplete="off">
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
        <div class="modal fade" id="editCountry" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Country</h4>       
                    </div>
                    <form method="post" id="editCountryForm">
                        @method('PATCH')
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="country_id">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                         <label class="form-label">Country Name</label>
                                        <input type="text" class="" name="name" placeholder="Country Name" autocomplete="off">
                                        
                                    </div>
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label class="form-label">Country Code</label>
                                        <input type="text" class="" name="code" placeholder="Country Code" autocomplete="off">
                                    </div>
                                </div>       
                                 <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label class="form-label">Country Currency</label>
                                        <input type="text" class="" name="currency" placeholder="Country Currency" autocomplete="off">
                                    </div>
                                </div>       
                                 <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label class="form-label">Currency Full Name</label>
                                        <input type="text" class="" name="currency_full_name" placeholder="Currency Full Name" autocomplete="off">
                                    </div>
                                </div>       
                                 <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                         <label class="form-label">Currency Symbol</label>
                                        <input type="text" class="" name="currency_symbol" placeholder="Currency Symbol" autocomplete="off">
                                    </div>
                                </div>       
                                 <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label class="form-label">Dialing Code</label>
                                        <input type="text" class="" name="dialing_code" placeholder="Dialing Code" autocomplete="off">
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
                    url: "{{ route('admin.getCountryList') }}",
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
                { data:'code',name:'code' },
                { data:'currency',name:'currency' },
                { data:'currency_full_name',name:'currency_full_name' },
                { data:'currency_symbol',name:'currency_symbol' },
                { data:'dialing_code',name:'dialing_code' },
                { data:'action',name:'action' },
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0,4],
                        // "bSortable": false
                    }
                ],

            });

            //ADD NEWS
            $('#addCountryForm').submit(function(e) {
                e.preventDefault();
            })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    name: { required: true },
                    currency: { required: true },
                    currency_symbol: { required: true },
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.country.store") }}',
                        data: formData,
                        processData:false,
                        cache:false,
                        contentType: false,
                        success: function(response)
                        {
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('input[name="name"] , input[name="currency"] , input[name="code"] , input[name="currency_full_name"] , input[name="currency_symbol"] , input[name="dialing_code"]').val('');
                                $('.preview-content').attr('src',' ')
                                $('#addCountry').modal('hide');
                                table.ajax.reload();
                            } else {
                                toastr.warning(response.message);
                            }
                        }
                    });
                }
            });
            function country_name(data){
                return data.country.name;
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
                            url: '{{ route("admin.country.destroy","/") }}/'+id,
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
                $("#editCountry input[name='country_id']").val($(this).data('id'));
                $("#editCountry input[name='name']").val($(this).data('name'));
                $("#editCountry input[name='code']").val($(this).data('code'));
                $("#editCountry input[name='currency']").val($(this).data('currency'));
                $("#editCountry input[name='currency_full_name']").val($(this).data('currency_full_name'));
                $("#editCountry input[name='currency_symbol']").val($(this).data('currency_symbol'));
                $("#editCountry input[name='dialing_code']").val($(this).data('dialing_code'));
                
                $('#editCountry').modal('show');
            });


            //EDIT NEWS
            $('#editCountryForm').submit(function(e) {
                e.preventDefault();
            })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    name: { required: true },
                    country_id: { required: true },
                    code: { required: true },
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    var id = $('input[name="city_id"]').val();
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.country.update","") }}/'+id,
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
                                 $('input[name="name"] , input[name="currency"] , input[name="code"] , input[name="currency_full_name"] , input[name="currency_symbol"] , input[name="dialing_code"]').val('');
                                $('.preview-content').attr('src',' ')
                                $('#editCountry').modal('hide');
                                table.ajax.reload();
                            } else {
                                toastr.warning(response.message);
                            }
                        }
                    });
                }
            });
        });
  $( function() {
    
    $( "#timezone" ).autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: '{{ route("admin.city.getTimezone","") }}',
          dataType: "json",
          data: {
            q: request.term
          },
          success: function( data ) { 
           response($.map(data.timezone, function (item) {
            console.log(item.timezone);
            return {
                label: item.timezone,
                value: item.timezone
            };
        }));
          }
        });
      },
      appendTo: $('.cityInsForm')
    });

     $( "#timezoneedit" ).autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: '{{ route("admin.city.getTimezone","") }}',
          dataType: "json",
          data: {
            q: request.term
          },
          success: function( data ) { 
           response($.map(data.timezone, function (item) {
            console.log(item.timezone);
            return {
                label: item.timezone,
                value: item.timezone
            };
        }));
          }
        });
      },
      appendTo: $('.cityInsFormEdit')
    });
  } );

  
    </script>
    @endsection