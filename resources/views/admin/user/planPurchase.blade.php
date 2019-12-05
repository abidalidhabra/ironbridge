@section('title','Ironbridge1779 | Plan Purchase')
@extends('admin.layouts.admin-app')
@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endsection
@section('content')
<div class="right_paddingboxpart">
    <div class="backbtn">
        <a href="{{ route('admin.userList') }}">Back</a>
    </div>
    <div class="srfrdbox">
        <h3>Plan Purchases</h3>
    </div>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover dt-responsive nowrap" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr.</th>
                    <th>Date</th>
                    <!-- <th>Name</th> -->
                    <th>Total Amount</th>
                    <th>Golds</th>
                    <th>Purchased Plan</th>
                    <th>Transaction ID</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
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
                    url: "{{ route('admin.getPlanPurchaseList') }}",
                    data: function ( d ) {
                        d._token = "{{ csrf_token() }}";
                        d.user_id = "{{ $id }}";
                    },
                    complete:function(){
                        //success code
                        if( $('[data-toggle="tooltip"]').length > 0 )
                        $('[data-toggle="tooltip"]').tooltip();

                    }
                },
                columns:[
                { data:'DT_RowIndex',name:'_id' },
                { data:'created_at',name:'created_at'},
                // { data:'name',name:'name' },
                { data:'total_amount',name:'total_amount' },
                { data:'gold_value',name:'gold_value' },
                { data:'purchased_plan',name:'purchased_plan' },
                { data:'transaction_id',name:'transaction_id' },
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