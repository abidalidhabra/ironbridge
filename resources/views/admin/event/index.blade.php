@section('title','Ironbridge1779 | Events')
@extends('admin.layouts.admin-app')
@section('styles')
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">      
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Events</h3>
            </div>
            @if(auth()->user()->hasPermissionTo('Add Event'))
                <div class="col-md-6 text-right modalbuttonadd">
                    <a href="{{ route('admin.event.basicDetails') }}" class="btn btn-info btn-md">Add Event</a>
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
                    <th>Event Name</th>
                    <th>Type</th>
                    <th>Coin Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>City</th>
                    @if(auth()->user()->hasPermissionTo('Edit Event') || auth()->user()->hasPermissionTo('Delete Event'))
                    <th width="5%">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody></tbody>
        </table>
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
                    type: "GET",
                    url: "{{ route('admin.getEventList') }}",
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
                { data:'name',name:'name' },
                { data:'type',name:'type' },
                { data:'coin_type',name:'coin_type' },
                { data:'starts_at',name:'starts_at' },
                { data:'ends_at',name:'ends_at' },
                { data:'city',name:'city' },
                @if(auth()->user()->hasPermissionTo('Add Event') || auth()->user()->hasPermissionTo('Delete Event'))
                { data:'action',name:'action' },
                @endif
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0,7],
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
                            url: '{{ route("admin.event.destroy","/") }}/'+id,
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