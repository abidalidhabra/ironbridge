@section('title','Ironbridge1779 | Rewards')
@extends('admin.layouts.admin-app')
@section('styles')
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">      
    <div class="">
    </div>
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Hunt Loot Tables</h3>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="rewardsmainbox">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#complexity_1">Difficulty 1</a></li>
            <li><a data-toggle="tab" href="#complexity_2">Difficulty 2</a></li>
            <li><a data-toggle="tab" href="#complexity_3">Difficulty 3</a></li>
            <li><a data-toggle="tab" href="#complexity_4">Difficulty 4</a></li>
            <li><a data-toggle="tab" href="#complexity_5">Difficulty 5</a></li>
        </ul>

        <div class="tab-content">
            <div id="complexity_1" class="tab-pane fade in active">
                <!-- <h3>Difficulty 1</h3> -->
                <br>
                @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))
                    <a href="javascript:void(0)" class="btn pull-right edit_reward default-btn" data-id="1">Edit</a>
                @endif
                @foreach($complexity1 as $key => $value)
                    <div class="rewardsbox">
                    <h4>{{ ucwords(str_replace('_',' ',$key)) }}</h4>
                        @foreach($value as $rewards)
                            @if($key == 'gold')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Gold</p>
                                        <h4>{{ $rewards['gold_value'] }} <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'skeleton_key')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Skeletons</p>
                                        <h4>{{ $rewards['skeletons'] }} <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'avatar_item')
                                <h5>Widgets order <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h5>
                                <h4>{{ $rewards['possibility'] }}%</h4>
                                @foreach($rewards['widgets_order'] as $widgets)
                                    <div class="col-md-3">
                                        <div class="smallrewardbox">
                                            <p>Widget</p>
                                            <h4>{{ $widgets['widget_name'].' ('.$widgets['type'].')' }}</h4>
                                        </div>
                                    </div>
                                @endforeach
                            @endif


                            @if($key == 'skeleton_key_and_gold')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Gold <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></p>
                                        <h4>{{ $rewards['gold_value'] }}</h4>
                                        <hr>
                                        <p>Skeletons</p>
                                        <h4>{{ $rewards['skeletons'] }}</h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'avatar_item_and_gold')
                                <h5>Widgets order <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h5>
                                <h4>{{ $rewards['possibility'] }}%</h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <div class="smallrewardbox">
                                                <p>Gold</p>
                                                <h4>{{ $rewards['gold_value'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @foreach($rewards['widgets_order'] as $widgets)
                                    <div class="col-md-3">
                                        <div class="smallrewardbox">
                                            <p>Widget</p>
                                            <h4>{{ $widgets['widget_name'].' ('.$widgets['type'].')' }}</h4>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div id="complexity_2" class="tab-pane fade">
                <!-- <h3>Difficulty 2</h3> -->
                <br>
                @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))
                    <a href="javascript:void(0)" class="btn pull-right edit_reward default-btn" data-id="2">Edit</a>
                @endif
                @foreach($complexity2 as $key => $value)
                    <div class="rewardsbox">
                    <h4>{{ ucwords(str_replace('_',' ',$key)) }}</h4>
                        @foreach($value as $rewards)
                            @if($key == 'gold')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Gold</p>
                                        <h4>{{ $rewards['gold_value'] }} <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'skeleton_key')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Skeletons</p>
                                        <h4>{{ $rewards['skeletons'] }} <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'avatar_item')
                                <h5>Widgets order <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h5>
                                <h4>{{ $rewards['possibility'] }}%</h4>
                                @foreach($rewards['widgets_order'] as $widgets)
                                    <div class="col-md-3">
                                        <div class="smallrewardbox">
                                            <p>Widget</p>
                                            <h4>{{ $widgets['widget_name'].' ('.$widgets['type'].')' }}</h4>
                                        </div>
                                    </div>
                                @endforeach
                            @endif


                            @if($key == 'skeleton_key_and_gold')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Gold <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></p>
                                        <h4>{{ $rewards['gold_value'] }}</h4>
                                        <hr>
                                        <p>Skeletons</p>
                                        <h4>{{ $rewards['skeletons'] }}</h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'avatar_item_and_gold')
                                <h5>Widgets order <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h5>
                                <h4>{{ $rewards['possibility'] }}%</h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <div class="smallrewardbox">
                                                <p>Gold</p>
                                                <h4>{{ $rewards['gold_value'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @foreach($rewards['widgets_order'] as $widgets)
                                    <div class="col-md-3">
                                        <div class="smallrewardbox">
                                            <p>Widget</p>
                                            <h4>{{ $widgets['widget_name'].' ('.$widgets['type'].')' }}</h4>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div id="complexity_3" class="tab-pane fade">
                <!-- <h3>Difficulty 3</h3> -->
                <br>
                @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))
                    <a href="javascript:void(0)" class="btn pull-right edit_reward default-btn" data-id="3">Edit</a>
                @endif
                @foreach($complexity3 as $key => $value)
                    <div class="rewardsbox">
                    <h4>{{ ucwords(str_replace('_',' ',$key)) }}</h4>
                        @foreach($value as $rewards)
                            @if($key == 'gold')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Gold</p>
                                        <h4>{{ $rewards['gold_value'] }} <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'skeleton_key')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Skeletons</p>
                                        <h4>{{ $rewards['skeletons'] }} <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'avatar_item')
                                <h5>Widgets order <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h5>
                                <h4>{{ $rewards['possibility'] }}%</h4>
                                @foreach($rewards['widgets_order'] as $widgets)
                                    <div class="col-md-3">
                                        <div class="smallrewardbox">
                                            <p>Widget</p>
                                            <h4>{{ $widgets['widget_name'].' ('.$widgets['type'].')' }}</h4>
                                        </div>
                                    </div>
                                @endforeach
                            @endif


                            @if($key == 'skeleton_key_and_gold')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Gold <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></p>
                                        <h4>{{ $rewards['gold_value'] }}</h4>
                                        <hr>
                                        <p>Skeletons</p>
                                        <h4>{{ $rewards['skeletons'] }}</h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'avatar_item_and_gold')
                                <h5>Widgets order <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h5>
                                <h4>{{ $rewards['possibility'] }}%</h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <div class="smallrewardbox">
                                                <p>Gold</p>
                                                <h4>{{ $rewards['gold_value'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @foreach($rewards['widgets_order'] as $widgets)
                                    <div class="col-md-3">
                                        <div class="smallrewardbox">
                                            <p>Widget</p>
                                            <h4>{{ $widgets['widget_name'].' ('.$widgets['type'].')' }}</h4>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div id="complexity_4" class="tab-pane fade">
                <!-- <h3>Difficulty 4</h3> -->
                <br>
                @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))
                    <a href="javascript:void(0)" class="btn pull-right edit_reward default-btn" data-id="4">Edit</a>
                @endif
                @foreach($complexity4 as $key => $value)
                    <div class="rewardsbox">
                    <h4>{{ ucwords(str_replace('_',' ',$key)) }}</h4>
                        @foreach($value as $rewards)
                            @if($key == 'gold')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Gold</p>
                                        <h4>{{ $rewards['gold_value'] }} <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'skeleton_key')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Skeletons</p>
                                        <h4>{{ $rewards['skeletons'] }} <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'avatar_item')
                                <h5>Widgets order <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h5>
                                <h4>{{ $rewards['possibility'] }}%</h4>
                                @foreach($rewards['widgets_order'] as $widgets)
                                    <div class="col-md-3">
                                        <div class="smallrewardbox">
                                            <p>Widget</p>
                                            <h4>{{ $widgets['widget_name'].' ('.$widgets['type'].')' }}</h4>
                                        </div>
                                    </div>
                                @endforeach
                            @endif


                            @if($key == 'skeleton_key_and_gold')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Gold <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></p>
                                        <h4>{{ $rewards['gold_value'] }}</h4>
                                        <hr>
                                        <p>Skeletons</p>
                                        <h4>{{ $rewards['skeletons'] }}</h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'avatar_item_and_gold')
                                <h5>Widgets order <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h5>
                                <h4>{{ $rewards['possibility'] }}%</h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <div class="smallrewardbox">
                                                <p>Gold</p>
                                                <h4>{{ $rewards['gold_value'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @foreach($rewards['widgets_order'] as $widgets)
                                    <div class="col-md-3">
                                        <div class="smallrewardbox">
                                            <p>Widget</p>
                                            <h4>{{ $widgets['widget_name'].' ('.$widgets['type'].')' }}</h4>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                @endforeach

            </div>
            <div id="complexity_5" class="tab-pane fade">
                <!-- <h3>Difficulty 5</h3> -->
                <br>
                @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))
                    <a href="javascript:void(0)" class="btn pull-right edit_reward default-btn" data-id="5">Edit</a>
                @endif
                @foreach($complexity5 as $key => $value)
                    <div class="rewardsbox">
                    <h4>{{ ucwords(str_replace('_',' ',$key)) }}</h4>
                        @foreach($value as $rewards)
                            @if($key == 'gold')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Gold</p>
                                        <h4>{{ $rewards['gold_value'] }} <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'skeleton_key')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Skeletons</p>
                                        <h4>{{ $rewards['skeletons'] }} <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'avatar_item')
                                <h5>Widgets order <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h5>
                                <h4>{{ $rewards['possibility'] }}%</h4>
                                @foreach($rewards['widgets_order'] as $widgets)
                                    <div class="col-md-3">
                                        <div class="smallrewardbox">
                                            <p>Widget</p>
                                            <h4>{{ $widgets['widget_name'].' ('.$widgets['type'].')' }}</h4>
                                        </div>
                                    </div>
                                @endforeach
                            @endif


                            @if($key == 'skeleton_key_and_gold')
                                <div class="col-md-3">
                                    <div class="smallrewardbox">
                                        <p>Gold <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></p>
                                        <h4>{{ $rewards['gold_value'] }}</h4>
                                        <hr>
                                        <p>Skeletons</p>
                                        <h4>{{ $rewards['skeletons'] }}</h4>
                                        <hr>
                                        <h4>{{ $rewards['possibility'] }}%</h4>
                                    </div>
                                </div>
                            @endif

                            @if($key == 'avatar_item_and_gold')
                                <h5>Widgets order <!-- @if(auth()->user()->hasPermissionTo('Edit Hunt Loot Tables'))<a href="javascript:void(0)" class="edit_reward" data-id="{{ $rewards->id }}"><i class="fa fa-pencil"></i></a>@endif --></h5>
                                <h4>{{ $rewards['possibility'] }}%</h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <div class="smallrewardbox">
                                                <p>Gold</p>
                                                <h4>{{ $rewards['gold_value'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @foreach($rewards['widgets_order'] as $widgets)
                                    <div class="col-md-3">
                                        <div class="smallrewardbox">
                                            <p>Widget</p>
                                            <h4>{{ $widgets['widget_name'].' ('.$widgets['type'].')' }}</h4>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>


<!-- EDIT MODEL -->
<div class="modal fade" id="editRewardModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Hunt Loot Tables</h4>
            </div>
            <form method="post" id="editrewardForm">
                
            </form>
        </div>

    </div>
</div>
<!-- END EDIT MODEL -->
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        
        /* EDIT MODEL SHOW */
        $(document).on('click','.edit_reward',function(){
            var id = $(this).attr('data-id');
            var url ='{{ route("admin.rewards.edit",':id') }}';
            url = url.replace(':id',id);
            $.ajax({
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                data: {id : id},
                success: function(response)
                {
                    //if (response.status == true) {
                        $('#editRewardModal').modal('show');
                        $('#editrewardForm').html(response);
                    /*} else {
                        toastr.warning(response.message);
                    }*/
                }
            });
        });
        /* END EDIT MODEL SHOW */
    
        /* UPDATE REWARDSD */
        $(document).on('submit','#editrewardForm',function(e){
            e.preventDefault();
            var id = $('#complexity').val();
            var url ='{{ route("admin.rewards.update",':id') }}';
            url = url.replace(':id',id);
            $.ajax({
                type: "PUT",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                data: $('#editrewardForm').serialize(),
                success: function(response)
                {
                    if (response.status == true) {
                        toastr.success(response.message);
                        location.reload(true);
                    } else {
                        toastr.warning(response.message);
                    }
                }
            });
        });    
        /* END UPDATE REWARDSD */
    
    });
</script>
@endsection