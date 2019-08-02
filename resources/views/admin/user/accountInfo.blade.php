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
                <div class="avtarimgtextiner boxheightset">
                    @if($data['widgetsIdSelected'][$widget->id] == true)
                        <span class="selectedtext">selected</span>
                    @endif
                    <span class="basdistext">{{ $widget->widget_category }}</span>
                    <img class="card-img-top" src="{{ asset('admin_assets/widgets/'.$widget->id.'.png') }}">
                    <div class="card-body">
                        <h5 class="card-title">
                            @if($widget->gold_price == '0')
                                Free gold
                            @else
                                {{ $widget->gold_price }} Gold
                            @endif
                        </h5>
                    </div>
                </div>
                @empty
                @endforelse
            @empty
            @endforelse

            
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
        });
    </script>
@endsection