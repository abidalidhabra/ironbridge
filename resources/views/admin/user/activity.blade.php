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
                        <p>{{ $data['goldPurchased'] }} Gold </p>
                    </div>
                </div>
                <!-- <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Gold Earned</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <p>0 Gold</p>
                    </div>
                </div> -->
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
                        @forelse($data['plan_purchase']->groupBy('country_code')  as $price)
                            <p>{{ number_format($price->pluck('price')->sum(),2).' '.$price[0]['country']['currency_full_name'] }}</p>
                        @empty
                            <p>No data found</p>
                        @endforelse
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
                        @forelse($data['plan_purchase']->groupBy('plan_id')  as $plane)
                            <p>{{ $plane[0]->gold_value.' Golds - '.number_format($plane[0]->price,2).' '.$plane[0]->country->currency .' ( '. count($plane) .' Times )'}}</p>
                        @empty
                            <p>No data found</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>


        <div class="datingactivity_box">
            <h3>Transaction History</h3>
            <div class="innerdatingactivity">
                <div class="swoped_detlisbox">
                    @forelse($data['plan_purchase'] as $plan_purchase)
                        <?php
                            /*echo "<pre>";
                            print_r($plan_purchase->toArray());
                            exit();*/
                        ?>
                        <div class="swoped_detlisleft">
                           <p>{{ ($plan_purchase->plan)?ucfirst($plan_purchase->plan->type):'-' }}</p> 
                        </div>
                        <div class="swoped_detlisright">
                            @if(isset($plan_purchase->gold_value))
                                <span>{{ $plan_purchase->gold_value }} Gold</span>
                            @endif
                            @if(isset($plan_purchase->skeleton_keys_amount))
                                <span>{{ $plan_purchase->skeleton_keys_amount }} Skeleton keys Amount</span>
                            @endif
                            
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