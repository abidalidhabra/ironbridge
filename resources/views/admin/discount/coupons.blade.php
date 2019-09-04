@section('title','Ironbridge1779 | Discount Coupons')
@extends('admin.layouts.admin-app')
@section('styles')
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">      
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Discount Coupons</h3>
            </div>
            @if(auth()->user()->hasPermissionTo('Add Discount Coupons'))
            <div class="col-md-6 text-right modalbuttonadd">
                <button type="button" class="btn btn-info btn-md" data-toggle="modal" data-target="#addDiscount">Add Discount</button>
            </div>
            @endif
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr.</th>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Discount</th>
                    <th>Number of uses</th>
                    <th>Can use Multitime ?</th>
                    <th>Start date</th>
                    <th>End date</th>
                    @if(auth()->user()->hasPermissionTo('Edit Discount Coupons') || auth()->user()->hasPermissionTo('Delete Discount Coupons'))
                        <th width="5%">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="container">  
    <div class="addDiscountmodal_box">
        <div class="modal fade" id="addDiscount" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Discount Coupons</h4>       
                    </div>
                    <form method="post" id="addDiscountForm">
                        @csrf
                        <div class="modal-body">
                            <div class="modalbodysetbox">
                                <div class="form-group">
                                    <label>Discount Code:</label>
                                    <input type="text" name="discount_code" class="form-control" placeholder="Enter Discount Code">
                                </div>
                                <div class="form-radio">
                                    <label>Discount Types:</label>
                                    <br/>
                                    <label class="radio-inline"><input type="radio" name="discount_types" value="gold_credit" checked>Gold Credits</label>
                                    <label class="radio-inline"><input type="radio" name="discount_types" value="discount_percentage">Discount Percentage</label>
                                    <label class="radio-inline"><input type="radio" name="discount_types" value="avatar_item">Avatar Item</label>
                                </div>
                                
                                <div id="avatar_item_box" class="allavtitembox avatar_item_box" style="display: none;">
                                    @forelse($widgetItem as $widget)
                                        @if(File::exists(public_path('admin_assets/widgets/'.$widget->id.'.png')))
                                        <div class="avt_itemboxset">
                                            <img class="card-img-top" src="{{ asset('admin_assets/widgets/'.$widget->id.'.png') }}">
                                            <div class="custboxslt">
                                                <label class="select_container">
                                                  <input type="checkbox" name="avatar_ids[]" value="{{ $widget->id }}">
                                                  <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </div>
                                        @endif
                                    @empty
                                    @endforelse
                                </div>
                                <div class="form-group discount_box">
                                    <label>Gold Credits:</label>
                                    <input type="text" name="discount" class="form-control" placeholder="Enter Gold Credits">
                                </div>
                                <div class="form-group">
                                    <label>Number Of Uses:</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" name="number_of_uses" class="form-control" placeholder="Enter number of uses">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="number_of_uses_checked">No Limit
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-radio mutitime_use" style="display: none;">
                                    <label>Single User Can Use Multiple Time?:</label>
                                    <br/>
                                    <label class="radio-inline"><input type="radio" name="can_mutitime_use" value="true" >Yes</label>
                                    <label class="radio-inline"><input type="radio" name="can_mutitime_use" value="false" checked>No</label>
                                </div>
                                <div class="form-group">
                                    <label>Expired In:</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" name="expiry_date" class="form-control" placeholder="Enter number of uses" autocomplete="off">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="expiry_date_checked">No Limit
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Description:</label>
                                    <textarea name="description" class="form-control"></textarea>
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
        <div class="modal fade" id="editDiscount" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Discount Coupon</h4>       
                    </div>
                    <form method="post" id="editDiscountForm">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <div class="modalbodysetbox">
                                <div id="discountEditContent"></div>
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
            /*  */
            $(document).on('change','[name=discount_types]',function(){
                var radio =  $(this).val();
                if (radio == 'gold_credit') {
                    $('.mutitime_use').hide();
                    $('.avatar_item_box').hide();
                    $('.discount_box').find('label').text('Gold Credits:');
                    $('.discount_box').find('input').attr('placeholder','Enter Gold Credits');
                } else if (radio == 'discount_percentage'){
                    $('.mutitime_use').show();
                    $('.avatar_item_box').hide();
                    $('.discount_box').find('label').text('Discount Percentage:');
                    $('.discount_box').find('input').attr('placeholder','Enter Discount Percentage');
                } else if (radio == 'avatar_item'){
                    $('.avatar_item_box').show();
                    $('.mutitime_use').hide();
                    $('.discount_box').find('label').text('Gold Credits:');
                    $('.discount_box').find('input').attr('placeholder','Enter Gold Credits');
                }
            });


            $(document).on('change','[name=number_of_uses_checked]',function(){
                if($(this).prop("checked") == true){
                    $('[name=number_of_uses]').prop("disabled", true);
                } else if($(this).prop("checked") == false){
                    $('[name=number_of_uses]').prop("disabled", false);   
                }
            });

            $(document).on('change','[name=expiry_date_checked]',function(){
                if($(this).prop("checked") == true){
                    $('[name=expiry_date]').prop("disabled", true);
                } else if($(this).prop("checked") == false){
                    $('[name=expiry_date]').prop("disabled", false);   
                }
            });


            /* DATE RANGE PICKER */
            dateRangePicker();
            function dateRangePicker(){
                $('input[name="expiry_date"]').daterangepicker({
                    autoUpdateInput: false,
                    autoApply: true,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'MMM D, YYYY',
                    }
                });

                  $('input[name="expiry_date"]').on('apply.daterangepicker', function(ev, picker) {
                      $(this).val(picker.startDate.format('MMM D, YYYY') + ' - ' + picker.endDate.format('MMM D, YYYY'));
                  });

                  $('input[name="expiry_date"]').on('cancel.daterangepicker', function(ev, picker) {
                      $(this).val('');
                  });

                // var start = moment().subtract(30, 'days');
                // var end = moment();
                /*function cb(start, end) {
                    $('input[name=expiry_date]').val(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
                }
                $('input[name=expiry_date]').daterangepicker({
                    startDate: start,
                    endDate: end,
                    autoUpdateInput:true,
                    autoApply: true,
                    locale: {
                        format: 'MMM D, YYYY',
                        cancelLabel: 'Clear'
                    },
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                },cb);
                cb(start, end);*/
            }


            //GET USER LIST
            var table = $('#dataTable').DataTable({
                pageLength: 10,
                processing: true,
                responsive: true,
                // serverSide: true,
                order: [],
                lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                ajax: {
                    type: "get",
                    url: "{{ route('admin.getDiscountsList') }}",
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
                { data:'discount_code',name:'discount_code'},
                { data:'discount_types',name:'discount_types'},
                { data:'discount',name:'discount' },
                { data:'number_of_uses',name:'number_of_uses' },
                { data:'can_mutitime_use',name:'can_mutitime_use' },
                { data:'start_at',name:'start_at' },
                { data:'end_at',name:'end_at' },
                @if(auth()->user()->hasPermissionTo('Edit Discount Coupons') || auth()->user()->hasPermissionTo('Delete Discount Coupons'))
                    { data:'action',name:'action' },
                @endif
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0],
                    }
                ],

            });

            //ADD NEWS
            $('#addDiscountForm').submit(function(e) {
                e.preventDefault();
            })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    discount_code: { required: true },
                    discount: { required: true },
                    number_of_uses: { required: true },
                    description: { required: true },
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    $.ajax({
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{ route("admin.discounts.store") }}',
                        data: formData,
                        processData:false,
                        cache:false,
                        contentType: false,
                        success: function(response)
                        {
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('input[name="discount_code"] , input[name="discount"] , input[name="number_of_uses"] , textarea ').val('');
                                $('input[name="discount_types"]:first').prop("checked",true);
                                $('input[name="can_mutitime_use"]:last').prop("checked",true);
                                $('.mutitime_use').hide();
                                $('.avatar_item_box').hide();
                                $('#addDiscount').modal('hide');
                                $('.discount_box').find('label').text('Gold Credits:');
                                $('.discount_box').find('input').attr('placeholder','Enter Gold Credits');
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
                $(".delete_discount").confirmation({
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
                            url: '{{ route("admin.discounts.destroy","/") }}/'+id,
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
            $(document).on('click', '.edit_discount', function(){
                var id = $(this).attr('data-id');
                var url ='{{ route("admin.discounts.edit",':id') }}';
                url = url.replace(':id',id);
                $.ajax({
                    type: "GET",
                    url: url,
                    beforeSend: function() {
                        $('#edit_admin'+id).find('i').addClass('fa-spinner fa-spin');
                    },
                    success: function(response)
                    {
                        $('#discountEditContent').html(response);
                        $('#editDiscount').modal('show');
                         dateRangePicker();
                        $('#edit_admin'+id).find('i').removeClass('fa-spinner fa-spin');
                   }
               });
            });


            //EDIT NEWS
            $('#editDiscountForm').submit(function(e) {
                e.preventDefault();
            })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    discount_code: { required: true },
                    discount: { required: true },
                    number_of_uses: { required: true },
                    description: { required: true },
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    var id = $('#discount_id').val();
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.discounts.update","") }}/'+id,
                        data: formData,
                        processData:false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        cache:false,
                        contentType: false,
                        beforeSend: function() {
                            //$('#editGameForm [type=submit]').html('<i class="fa fa-spinner fa-spin"></i> Save');
                        },
                        success: function(response)
                        {
                            
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('input[name="discount_code"] , input[name="discount"] , input[name="number_of_uses"] , textarea ').val('');
                                $('input[name="discount_types"]:first').prop("checked",true);
                                $('input[name="can_mutitime_use"]:last').prop("checked",true);
                                $('.discount_box').find('label').text('Gold Credits:');
                                $('.discount_box').find('input').attr('placeholder','Enter Gold Credits');
                                $('.mutitime_use').hide();
                                $('.avatar_item_box').hide();
                                $('#editDiscount').modal('hide');
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