@section('title','Ironbridge1779 | Payment')
@extends('admin.layouts.admin-app')
@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endsection
@section('content')
<div class="right_paddingboxpart">
    <div class="centr_paretboxpart">
        <div class="signeup_topbox">
            <div class="signeup_lefttextbox">
                <p>Payments</p>
            </div>
        </div>
        <div class="signeup_innerborderbox">
            <div class="total_usersdetlis">
                <ul>
                    <li>
                        <img src="{{ asset('admin_assets/svg/user.svg') }}">
                        <h3>${{ $data['collected'] }}</h3>
                        <p>Collected</p>
                    </li>
                    <!-- <li>
                        <img src="http://localhost/ironbridge1779/public/admin_assets/svg/apple-icon.svg">
                        <h3>{{ $data['ios'] }}</h3>
                        <p>IOS</p>
                    </li>
                    <li>
                        <img src="http://localhost/ironbridge1779/public/admin_assets/svg/android-icon.svg">
                        <h3>{{ $data['android'] }}</h3>
                        <p>Android</p>
                    </li> -->
                </ul>
            </div>
        </div>
    </div>      
    
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover dt-responsive nowrap" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr.</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Total Amount</th>
                    <!-- <th>Income</th> -->
                    <!-- <th>Fees</th> -->
                    <th>Golds</th>
                    <th>Purchased Plan</th>
                    <!-- <th>Type</th> -->
                    <th>Transaction ID</th>
                    <!-- <th>Payment Gateway</th> -->
                    @if(auth()->user()->hasPermissionTo('View Users'))
                        <th>View Profile</th>
                    @endif
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
                    url: "{{ route('admin.getPaymentList') }}",
                    data: function ( d ) {
                        d._token = "{{ csrf_token() }}";
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
                { data:'name',name:'name' },
                { data:'total_amount',name:'total_amount' },
                // { data:'income',name:'income' },
                // { data:'fees',name:'fees' },
                { data:'gold_value',name:'gold_value' },
                { data:'purchased_plan',name:'purchased_plan' },
                // { data:'type',name:'type' },
                { data:'transaction_id',name:'transaction_id' },
                // { data:'payment',name:'payment' },
                @if(auth()->user()->hasPermissionTo('View Users'))
                { data:'action',name:'action' },
                @endif
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0,4],
                    }
                ],

            });
        });
    </script>
    @endsection