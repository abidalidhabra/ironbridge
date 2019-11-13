@section('title','Ironbridge1779 | Agent Levels')
@extends('admin.layouts.admin-app')

@section('style')

@endsection

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="">               
            <div class="col-md-12 text-right">
                <a href="{{ route('admin.relicReward.index') }}" class="btn back-btn">Back</a>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <h3>Agent Levels: </h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box allboxinercoversgm" id="addSeasonContainer">
        <div class="seastaicoboxsettop">
            <!-- <h2>Relics Detail:</h2> -->
            <p>Agent Level: <span>{{ (isset($relicReward->agent_level))?$relicReward->agent_level:'-' }}</span> </p>
            <p>XP Points: <span>{{ (isset($relicReward->xps)?$relicReward->xps:'-') }}</span> </p>
            <p>Difficulty: <span>{{ (isset($relicReward->complexity)?$relicReward->complexity:'-') }}</span> </p>
        </div>
        <div class="relisetinerript">
            <h2>Minigames</h2>
            @forelse($games as $index => $game)
                <h4>{{ ($index+1).' - '.$game }} </h4>
            @empty
                <h4>No Data Found</h4>
            @endforelse
        </div>
        <div class="relisetinerript">
            <h2>widgets</h2>
            @forelse($relicReward->widgets as $key => $widgets)
                <h4>{{ ucfirst($key) }} </h4>
                    @if(!is_null($widgets) && count($widgets) > 0)
                        <?php
                            $widgetItem = \DB::table('widget_items')->whereIn('_id',$widgets)->get();
                        ?>  
                        @forelse($widgetItem as $key => $widget)
                        <div class="col-md-3">
                            @if (file_exists(public_path('admin_assets/widgets/'.$widget['_id'].'.png')))
                                <img src="{{ asset('admin_assets/widgets/'.$widget['_id'].'.png') }}" style="width: 100%;height: auto;">
                            @else
                                <img src="{{ asset('admin_assets/images/no_image.png') }}" style="width: 100%;height: auto;">
                            @endif
                        </div>
                        @empty
                            <h4>No Data Found</h4>  
                        @endforelse
                    @else
                        <p>No Data Found</p>
                    @endif
             
            @empty
                <h4>No Data Found</h4>
            @endforelse
        </div>
      
    </div>
</div>
@endsection

@section('scripts')

<script>
    $(document).on('submit', '#addSeasonForm', function(e) {
        e.preventDefault();
        if(validate()) {
            $.ajax({
                type: "POST",
                url: '{{ route('admin.seasons.store') }}',
                data: new FormData(this),
                contentType: false,
                processData: false,
                cache: false,
                success: function(response)
                {
                    if (response.status == true) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = '{{ route('admin.sponser-hunts.index') }}';
                        }, 2000)
                    } else {
                        toastr.warning('You are not authorized to access this page.');
                    }
                },
                error: function(xhr, exception) {
                    let error = JSON.parse(xhr.responseText);
                    toastr.error(error.message);
                }
            });
        }
    });

    // function initializeDeletePopup() {
        $("a[data-action='delete']").confirmation({
            container:"body",
            btnOkClass:"btn btn-sm btn-success",
            btnCancelClass:"btn btn-sm btn-danger",
            onConfirm:function(event, element) {
                event.preventDefault();
                $.ajax({
                    type: "DELETE",
                    url: element.attr('href'),
                    success: function(response){
                        if (response.status == true) {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            }
        }); 
    // }
</script>
@endsection