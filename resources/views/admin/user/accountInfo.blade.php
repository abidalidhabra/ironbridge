@section('title','Ironbridge1779 | User')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="accountinfo_paretbox">
            <div class="backbtn">
                <a href="{{ route('admin.userList') }}">Back</a>
            </div>
            <div class="accounttital_text">
                <h3>Account Info</h3>
                <div class="accountinfo_leftbox">
                    <div class="accountinfo_child">
                        <div class="accountinfoname_detlis">
                            <div class="accountinfoname_left">
                                <p>Name</p>
                                <h4>{{ $user->first_name.' '.$user->last_name }}</h4>
                            </div>
                        </div>
                        <div class="accountinfoname_detlis">
                            <div class="accountinfoname_left">
                                <p>Date of birth</p>
                                <h4>{{ $user->dob->format('d-M-Y') }}</h4>
                            </div>
                            <div class="accountinfoname_left">
                                <p>Gender</p>
                                <h4>{{ ($user->gender)?$user->gender:'-' }}</h4>
                            </div>
                        </div>
                         <div class="accountinfoname_detlis">
                            <div class="accountinfoname_left">
                                <p>Username</p>
                                <h4>{{ $user->username }}</h4>
                            </div>                            
                        </div>

                    </div>
                    <div class="accountinfo_child">
                        <div class="accountinfoname_detlis">
                            <div class="accountinfoname_left">
                                <p>Email Address</p>
                                <h4>{{ $user->email }}</h4>
                            </div>
                            <div class="accountinfoname_left">
                                <p>Gold</p>
                                <h4>{{ $data['currentGold'] }}</h4>
                            </div>                           
                        </div>
                        <div class="accountinfoname_detlis">
                            <div class="accountinfoname_left">
                                <p>Available Skeleton Keys</p>
                                <h4>{{ $data['skeleton'] }}</h4>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="accountinfo_paretboxone">
                <h3>Registration info</h3>
                <div class="accountinfo_rightbox">
                    <div class="accountcerated_text">
                        <p>Account Cerated on</p>
                        <h4>{{ $user->created_at->format('d-M-Y @ h:i a') }}</h4>
                    </div>
                    <div class="accountcerated_text">
                        <p>Time Zone</p>
                        <h4>-</h4>
                    </div>
                    <!-- <div class="accountcerated_text">
                        <p>App Installed Status</p>
                        <h4>Uninstalled</h4>
                    </div> -->
                </div>
            </div>
        </div>
        <div class="avtardetailbox">
            @forelse($data['widget'] as $key => $widgetlist)
                <h4>{{ $key }}</h4>
                @forelse($widgetlist as $widget)
                <div class="avtarimgtextiner">
                    <img class="card-img-top" src="{{ asset('admin_assets/images/FullDressup.png') }}">
                    <div class="card-body">
                        <h5 class="card-title">${{ $widget->gold_price }}</h5>
                        <p class="card-text">{{ $widget->id}}</p>
                    </div>
                </div>
                @empty
                @endforelse
            @empty
            @endforelse

            
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