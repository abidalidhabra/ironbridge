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
                <h3>Event Participated</h3>
            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr.</th>
                    <th>Event Name</th>
                    <th>Name</th>
                    <th>City</th>
                    <th>Completed Date</th>
                    <th>Status</th>
                    @if(auth()->user()->hasPermissionTo('View Users'))
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
                // serverSide: true,
                order: [],
                lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                ajax: {
                    type: "GET",
                    url: "{{ route('admin.getEventParticipatedList') }}",
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
                { data:'event_name',name:'event_name' },
                { data:'user_name',name:'user_name' },
                { data:'city',name:'city' },
                { data:'completed_at',name:'completed_at' },
                { data:'status',name:'status' },
                @if(auth()->user()->hasPermissionTo('View Users'))
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
        });
    </script>
    @endsection