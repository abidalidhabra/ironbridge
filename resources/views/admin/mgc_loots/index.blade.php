@section('title','Ironbridge1779 | Loots')
@extends('admin.layouts.admin-app')
@section('styles')
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<style type="text/css">
    .select2-container{display: initial !important;}
</style>
<div class="right_paddingboxpart">      
    <div class="">
    </div>
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Manage MGC Loot Tables</h3>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="rewardsmainbox">
        <div class="tab-content">
            <div class="tab-pane fade in active">                
                @if(auth()->user()->hasPermissionTo('Edit Loot'))
                    <a href="javascript:void(0)" class="btn pull-right edit_reward default-btn" data-id="">Edit</a>
                @endif
                    <div class="rewardsbox">
                        <h4>Relics</h4>
                        <div class="col-md-3">
                            <div class="smallrewardbox">
                                @forelse($loot[0]->relics_info as $key => $relic)
                                    <p> {{ ($key+1).' - '.$relic->name.'('.$relic->number.')' }}</p>
                                @empty
                                @endforelse
                            </div>
                        </div>  
                    </div>
                @forelse($loots as $key => $value)
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
                @empty
                    <div class="rewardsbox">
                        <h4>No Data Found</h4>
                    </div>
                @endforelse
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
                <h4 class="modal-title">Edit MCG Loot Tables</h4>
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
        
        $('#relics').select2();

        @if(Session::has('action') && Session::get('action')=='edit_model')
            editmodel({{$id}});
        @endif
        
        /* END EDIT MODEL SHOW */
        $(document).on('click','.edit_reward',function(){
            var url ='{{ route("admin.mgc_loot.edit",1) }}';
            // url = url.replace(':id',id);
            $.ajax({
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                data: {id : 1},
                beforeSend: function() {    
                    $('body').css('opacity','0.5');
                },
                success: function(response)
                {
                    $('body').css('opacity','1');
                    //if (response.status == true) {
                        $('#editRewardModal').modal('show');
                        $('#editrewardForm').html(response);
                        $('#relics').select2();
                    /*} else {
                        toastr.warning(response.message);
                    }*/
                }
            });
        })
        /* UPDATE REWARDSD */
        $(document).on('submit','#editrewardForm',function(e){
            e.preventDefault();
            var id = 1;
            var url ='{{ route("admin.mgc_loot.update",':id') }}';
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
                        window.location.href = '';
                    } else {
                        toastr.warning(response.message);
                    }
                },error: function(xhr, exception) {
                    let error = JSON.parse(xhr.responseText);
                    toastr.error(error.message);
                }
            });
        });    
        /* END UPDATE REWARDSD */
    
    });
</script>
@endsection