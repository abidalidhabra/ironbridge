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
                                <h4>{{ ($user->dob)?date('d-m-Y',strtotime($user->dob)):'-' }}</h4>
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
                                <p>Available Gold Balance
                                    <a href="javascript:void(0)" data-action='btnAdd' data-id="{{ $id }}"><i class="fa fa-plus"></i></a>
                                </p>
                                <h4 id="currentGold">{{ $data['currentGold'] }}</h4>
                            </div>                           
                        </div>
                        <div class="accountinfoname_detlis">
                            <div class="accountinfoname_left">
                                <p>Available Skeleton Keys 
                                    <a href="javascript:void(0)" data-action="skeleton" data-id="{{ $id }}"><i class="fa fa-plus"></i></a>
                                </p>
                                <h4 class="skeleton_text">{{ $data['skeleton'] }}</h4>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="accountinfo_paretboxone">
                <h3>Registration info</h3>
                <div class="accountinfo_rightbox">
                    <div class="accountcerated_text">
                        <p>Account Created on</p>
                        <h4>{{ $user->created_at->format('d-M-Y @ h:i a') }}</h4>
                    </div>
                    @if($user->agent_status)
                    <div class="accountcerated_text">
                        <p>Agent Level</p>
                        <h4>{{ $user->agent_status['level'] }}</h4>
                    </div>
                    <div class="accountcerated_text">
                        <p>XP</p>
                        <h4>{{ number_format($user->agent_status['xp']) }}</h4>
                    </div>
                    @endif
                </div>
            </div>
            @if($user->device_info)
                <div class="accountinfo_paretboxone">
                    <h3>Device info</h3>
                    <div class="accountinfo_rightbox">
                        <div class="accountcerated_text">
                            <p>Device</p>
                            @if($user->device_info['type'] == 'android')
                                <h4>Android</h4>
                                <!-- <img src="{{ asset('admin_assets/images/android.png') }}" style="height: 50px"> -->
                            @else
                                <h4>iOS</h4>
                                <!-- <img src="{{ asset('admin_assets/images/ios.png') }}" style="height: 50px"> -->
                            @endif
                        </div>
                        <div class="accountcerated_text">
                            <p>Model</p>
                            <h4>{{ $user->device_info['model'] }}</h4>
                        </div>
                        <div class="accountcerated_text">
                            <p>OS</p>
                            <h4>{{ $user->device_info['os'] }}</h4>
                        </div>
                    </div>
                </div>
             @endif
        </div>
        <div class="avtardetailbox">
                <h4>Relics</h4>
                @forelse($relics as $key => $relic)
                    @php
                        $percentage = ($relic->collected_pieces/$relic->pieces)*100;
                        $pieces = $percentage.'%';
                        if($relic->acquired){
                            if($relic->acquired['status'] == true){
                                $opacity = 1;
                                if($percentage == 100){
                                    $pieces = 'Activated';
                                }
                            } else {
                                $opacity = 0.8;
                            }
                        }else{
                            $opacity = 0.4;
                        }

                        
                    @endphp
                    <div class="avtarimgtextiner boxheightset">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped @if($percentage != 100) {{'active'}} @endif" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:{{ $percentage }}%">
                              {{ $pieces }}
                            </div>
                        </div>
                        <div  style="opacity: {{ $opacity }}">
                            <img class="card-img-top" style="object-fit: scale-down" src="{{ ($relic->icon)?$relic->icon:asset('admin_assets/images/no_image.png') }}">
                            <div class="card-body">
                                <h5 class="card-title">
                                    {{ $relic->name }}
                                </h5>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse            
        </div>
         <div class="avtardetailbox">
            <h4>Basic Update</h4>
            <form method="post" id="updateCityForm">
                @csrf
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Home City</label>
                        <select class="form-control" name="city">
                                <option value="">Please Select</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" @if($user->city_id == $city->id){{ 'selected' }}@endif>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Date Of Birth</label>
                        <input type="text" class="form-control" value="{{ Carbon\Carbon::parse($user->dob)->format('d-m-Y') }}" placeholder="Enter the date of birth" name='dob' id="dateofbirth">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ADD GOLD MODEL -->
    <!-- Modal -->
    <div class="modal fade" id="addgoldModel" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Gold</h4>
                </div>
                <form method="post" id="addGoldForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Add Gold</label>
                            <input type="hidden" name="id" id="id">
                            <input type="number" class="form-control" placeholder="Enter the gold" name='gold' id="gold">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ADD SKELETON KEY -->
    <div class="modal fade" id="addskeletonModel" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Skeleton Keys</h4>
                </div>
                <form method="post" id="addSekelotonForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Add Skeleton Keys</label>
                            <input type="hidden" name="user_id" id="user_id">
                            <input type="number" class="form-control" placeholder="Enter the skeleton keys" name='skeleton_key' id="skeleton_key">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
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


            //ADD GOLD MODEL SHOW
            $("body").delegate("a[data-action='btnAdd']", "click", function() {
                var id = $(this).attr('data-id');
                $('#id').val(id);
                $('#addgoldModel').modal('show');
            });

            $("body").delegate('a[data-action="skeleton"]', "click", function() {
                var id = $(this).attr('data-id');
                $('#user_id').val(id);
                $('#addskeletonModel').modal('show');
            }); 
            
             //ADD GOLD 
            $('#addGoldForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: '{{ route("admin.addGold") }}',
                    data: $(this).serialize(),
                    success: function(response)
                    {
                        if (response.status == true) {
                            toastr.success(response.message);
                            $('#addgoldModel').modal('hide');
                            $('#gold , #id').val('');
                            $('#currentGold').text(response.current_gold);
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            });

            //ADD Skeleton
            $('#addSekelotonForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: '{{ route("admin.addSkeletonKey") }}',
                    data: $(this).serialize(),
                    success: function(response)
                    {
                        if (response.status == true) {
                            toastr.success(response.message);
                            $('#addskeletonModel').modal('hide');
                            $('#skeleton_key , #user_id').val('');
                            
                            $('.skeleton_text').text(response.available_skeleton_keys)
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            });

            $('#dateofbirth').datepicker({
                format: 'dd-mm-yyyy',
                endDate: '-1d'
            })
             $('#updateCityForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: '{{ route("admin.updateCity") }}',
                    data: $(this).serialize()+'&user_id='+ '{{ $id }}',
                    success: function(response)
                    {
                        if (response.status == true) {
                            toastr.success(response.message);
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            });
        });
    </script>
@endsection