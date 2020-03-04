@section('title','Ironbridge1779 | NEWS')
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
                <h3>Cities</h3>
            </div>
         
            <div class="col-md-6 text-right modalbuttonadd">
                <button type="button" class="btn btn-info btn-md" data-toggle="modal" data-target="#addCity">Add City</button>
            </div>
         
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr.</th>
                    <th>City Name</th>
                    <th>State Name</th>
                    <th>Country</th>
                    <th>Timezone</th>
                    <th width="5%">Action</th>
                    
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="container">  
    <div class="addnewsmodal_box">
        <div class="modal fade" id="addCity" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add City</h4>       
                    </div>
                    <form method="post" id="addCityForm">
                        @csrf
                        <div class="modal-body">
                            <div class="modalbodysetbox">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label class="form-label">Country</label>
                                        <select onchange="getstateByCountry(this.value,'addStasteData')" name="country_id" class="form-control">
                                          <option value="">Select Contry</option>
                                          @foreach($country as $cnt)
                                          <option value="{{ $cnt->id }}">{{ $cnt->name }}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                </div>   
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label class="form-label">State</label>
                                        <select name="state_id" id="addStasteData" class="form-control">
                                          <option value="">Select State</option>
                                           
                                        </select>
                                    </div>
                                </div>      
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                        <label class="form-label">City Name</label>
                                        <input type="text" class="" name="name" placeholder="City Name" autocomplete="off">
                                        
                                    </div>
                                </div>       
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">     
                                       <label class="form-label">Timezone</label>
                                        <input type="text" class="form-control" id="timezone" name="timezone" placeholder="Timezone" data-class="cityInsForm"  autocomplete="off">
                                        <div class="cityInsForm"></div>
                                    </div>
                                </div>
                                
                                <!-- <div class="newstitlebox_inputbox">
                                    <div class="form-group">                  
                                        <input type="file" name="image">
                                    </div>
                                </div> -->

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
        <div class="modal fade" id="editCity" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit City</h4>       
                    </div>
                    <form method="post" id="editCityForm">
                        @method('PATCH')
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="city_id">
                            <div class="modalbodysetbox">
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                         <label class="form-label">Country</label>
                                        <select onchange="getstateByCountry(this.value,'editStasteData')" name="country_id" class="form-control">
                                          <option value="">Select Contry</option>
                                          @foreach($country as $cnt)
                                          <option value="{{ $cnt->id }}">{{ $cnt->name }}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                </div>  
                                 <div class="form-group">
                                     <label class="form-label">State</label>
                                        <select id="editStasteData" name="state_id" class="form-control">
                                          <option value="">Select State</option>
                                          @foreach($state as $st)
                                          <option value="{{ $st->id }}">{{ $st->name }}</option>
                                          @endforeach
                                        </select>
                                    </div>           
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">
                                         <label class="form-label">City</label>
                                        <input type="text" class="" name="name" placeholder="City Name" autocomplete="off">
                                        
                                    </div>
                                </div>
                                <div class="newstitlebox_inputbox">
                                    <div class="form-group">      
                                        <label class="form-label">Timezone</label>            
                                        <input type="text" class="form-control" id="timezoneedit" name="timezone" placeholder="Timezone" data-class="cityInsFormEdit" autocomplete="off">
                                         <div class="cityInsFormEdit"></div>
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
                    url: "{{ route('admin.getCityList') }}",
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
                { data:state_name,name:'state_name' },
                { data:country_name,name:'country_name' },
                { data:'timezone',name:'timezone' },
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
            $('#addCityForm').submit(function(e) {
                e.preventDefault();
            })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    country_id: { required: true },
                    name: { required: true },
                    timezone: { required: true },
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.city.store") }}',
                        data: formData,
                        processData:false,
                        cache:false,
                        contentType: false,
                        dataType: "json",
                        success: function(response)
                        {
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('input[name="name"] , textarea[name="timezone"],select[name="state_id"] ,select[name="country_id"]').val('');
                                $('.preview-content').attr('src',' ')
                                $('#addCity').modal('hide');
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
                 function state_name(data){
                     if(data.state){
                    return data.state.name;    
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
                            url: '{{ route("admin.city.destroy","/") }}/'+id,
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
                $("#editCity input[name='city_id']").val($(this).data('id'));
                $("#editCity input[name='name']").val($(this).data('cityname'));
                $("#editCity select[name='country_id']").val($(this).data('country'));
                $("#editCity select[name='state_id']").val($(this).data('state'));
                
                $("#editCity input[name='timezone']").val($(this).data('timezone'));
                
                $('#editCity').modal('show');
            });


            //EDIT NEWS
            $('#editCityForm').submit(function(e) {
                e.preventDefault();
            })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    name: { required: true },
                    country_id: { required: true },
                    timezone: { required: true },
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    var id = $('input[name="city_id"]').val();
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.city.update","") }}/'+id,
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
                                $('input[name="name"] , select[name="country_id"] ,select[name="state_id"] ,  input[name="timezone"]').val('');
                                $('.preview-content').attr('src',' ')
                                $('#editCity').modal('hide');
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
      source: @json($tz)
      ,change: function(event, ui){
            if(ui.item){
              //user select an item
            }
            else{
            jQuery('#timezone').val('');
            }},
      appendTo: $('.cityInsForm')
    });

    $( "#timezoneedit" ).autocomplete({
        source: @json($tz),
        change: function(event, ui){
            if(ui.item){
                //user select an item
            }else{
                jQuery('#timezoneedit').val('');
            }
        },
        appendTo: $('.cityInsFormEdit')
    });
});
  //    $( "#timezoneedit" ).autocomplete({
  //     source: function( request, response ) {
  //       $.ajax({
  //         url: '{{ route("admin.city.getTimezone","") }}',
  //         dataType: "json",
  //         data: {
  //           q: request.term
  //         },
  //         success: function( data ) { 
  //          response($.map(data.timezone, function (item) {
  //           console.log(item.timezone);
  //           return {
  //               label: item.timezone,
  //               value: item.timezone
  //           };
  //       }));
  //         }
  //       });
  //     },change: function(event, ui){
  //           if(ui.item){
  //             //user select an item
  //           }
  //           else{
  //           jQuery('#timezoneedit').val('');
  //           }},
  //     appendTo: $('.cityInsFormEdit')
  //   });
  // } );

    function getstateByCountry(country_id,id){

        $.ajax({
                type: "POST",
                url: '{{ route("admin.countryState") }}',
                data: {country_id:country_id},
               
                success: function(response)
                {  jQuery('#'+id).html('');
                    if (response.status == true) {
                        $.each(response.state, function(key, value) {
                             jQuery('#'+id)
                             .append($('<option>', { value : value._id })
                                  .text(value.name));
                        });

                    } else {
                        toastr.warning(response.message);
                    }
                }
            });
    }
    </script>
    @endsection