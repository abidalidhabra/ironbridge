@section('title','Ironbridge1779 | User')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="datingactivity_box">
            <div class="backbtn">
                <a href="{{ route('admin.userList') }}">Back</a>
            </div>
            <h3>Transactions Activity</h3>
            <div class="innerdatingactivity">
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Current Balance</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <span>{{ $data['currentGold'] }} Gold</span>
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Total Gold</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <p>{{ $data['totalGold'] }} Gold</p>
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Gold Purchased</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <p>0 Gold </p>
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Gold Earned</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <p>0 Gold</p>
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Used Gold</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <p>{{ $data['usedGold'] }} Gold</p>
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Total Money Spent</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <p></p>
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Purchase Device</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <p>Android ( 0 Times )</p>
                        <p>iOS ( 0 Times )</p>
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Purchased Packages</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <p>No data found</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="datingactivity_box">
            <h3>Transaction History</h3>
            <div class="innerdatingactivity">
                <div class="swoped_detlisbox">
                    @forelse($data['plan_purchase'] as $plan_purchase)
                       <div class="swoped_detlisleft">
                           <p>{{ ucfirst($plan_purchase->plan->type) }}</p> 
                        </div>
                        <div class="swoped_detlisright">
                            <span>{{ $plan_purchase->gold_value }} Gold</span>
                            <p>( {{ $plan_purchase->price .' '.$plan_purchase->country->currency }} )</p>
                            <p>{{ $plan_purchase->created_at->format('d-m-Y @ h:i a') }}</p>
                        </div>
                    @empty
                        <div class="swoped_detlisleft">
                           <p>No data found</p> 
                        </div>
                    @endforelse
                </div>                
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <!-- <script type="text/javascript" src="{{ asset('js/toastr.min.js') }}"></script> -->
    <script type="text/javascript">
        $(document).ready(function() {
            //GET USER LIST
            var table = $('#dataTable').DataTable({
                pageLength: 50,
                processing: true,
                responsive: true,
                serverSide: true,
                order: [[1, 'desc']],
                lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                ajax: {
                    type: "get",
                    url: "{{ route('admin.getUsers') }}",
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
                    { data:'created_at',name:'created_at'},
                    { data:'name',name:'name' },
                    { data:'email',name:'email' },
                    { data:'username',name:'username' },
                    { data:'dob',name:'dob'},
                ],

            });
            
        });
    </script>
@endsection