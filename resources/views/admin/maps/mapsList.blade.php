@section('title','Ironbridge1779 | Maps')
@extends('admin.layouts.admin-app')
@section('styles')
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Treasure hunt locations</h3>
            </div>
            @if(auth()->user()->hasPermissionTo('Add Treasure Locations'))
            <div class="col-md-6 text-right modalbuttonadd">
                <a href="{{ route('admin.add_location') }}" class="btn btn-info btn-md">Add Location</a>
            </div>
            @endif
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th>Sr.</th>
                    <th>Update Date</th>
                    <th>Name</th>
                    <th>Place Name</th>
                    <th>Verified</th>
                    <th width="11%">In-Progress</th>
                    <th>City</th>
                    <th>Map</th>
                    @if(auth()->user()->hasPermissionTo('Edit Treasure Locations') || auth()->user()->hasPermissionTo('Delete Treasure Locations'))
                    <th>Action</th>
                    @endif
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<!-- <script type="text/javascript" src="{{ asset('js/toastr.min.js') }}"></script> -->
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
                    url: "{{ route('admin.getMaps') }}",
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
                { data:'updated_at',name:'updated_at' },
                { data:'name',name:'name' },
                { data:'place_name',name:'place_name' },
                { data:'verified',name:'verified' },
                // { data:'province',name:'province' },
                { data:'progress_hunt',name:'progress_hunt' },
                { data:'city',name:'city' },
                { data:'map',name:'map'},
                @if(auth()->user()->hasPermissionTo('Edit Treasure Locations') || auth()->user()->hasPermissionTo('Delete Treasure Locations'))
                { data:'action',name:'action'},
                @endif
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0,8],
                    }
                ],

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
                            url: '{{ route("admin.locationDelete","/") }}/'+id,
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
        });
    </script>
    @endsection