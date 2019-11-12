@section('title','Ironbridge1779 | Relic Creation')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="">               
            <div class="col-md-12 text-right">
                <a href="{{ route('admin.relicReward.index') }}" class="btn back-btn">Back</a>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <h3>Edit Agent Complementary</h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <form method="POST" id="editRelicRewardForm">
            @csrf
            @method('PUT')
            <div class="modal-body padboxset">
                <div class="modalbodysetbox">
                    <div class="addrehcover">
                        <div class="form-group @error('agent_level') has-error @enderror">
                            <label class="control-label">Agent Level:</label>
                            <input type="number" class="form-control" 
                            name="agent_level" value="{{ $relicReward->agent_level }}" placeholder="Enter The Agent Level">
                            @error('agent_level')
                            <div class="text-muted text-danger"> {{ $errors->first('agent_level') }} </div>
                            @enderror
                        </div>
                        <div class="form-group @error('xps') has-error @enderror">
                            <label class="control-label">XP Points:</label>
                            <input type="number" class="form-control" 
                            name="xps" placeholder="Enter The XP Points" value="{{ $relicReward->xps }}">
                            @error('xps')
                            <div class="text-muted text-danger"> {{ $errors->first('xps') }} </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Minigames:</label>
                            <select name="minigames[]" class="form-control" id="minigames" multiple="multiple">
                                <option value="">Select Minigames</option>
                                @forelse($games as $game)
                                <option value="{{ $game->id }}" @if(in_array($game->id,$relicReward->minigames)){{ 'selected' }}@endif >{{ $game->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>

                        <div class="form-group @error('xps') has-error @enderror">
                            <label class="control-label">Difficulty:</label>
                            <select name="complexity" class="form-control">
                                <option value="">Select Difficulty</option>
                                @for($i=1;$i <= 5;$i++)
                                    <option value="{{ $i }}" @if($i==$relicReward->complexity){{ 'selected' }}@endif>{{ $i }}</option>
                                @endfor
                            </select>
                            @error('complexity')
                            <div class="text-muted text-danger"> {{ $errors->first('complexity') }} </div>
                            @enderror
                        </div>
                        <div class="form-group @error('xps') has-error @enderror">
                            <label class="control-label">Bucket Size:</label>
                            <select name="bucket_size" class="form-control">
                                <option value="">Select Bucket Size</option>
                                @for($i=1;$i <= 10;$i++)
                                    <option value="{{ $i }}" @if($i==$relicReward->bucket_size){{ 'selected' }}@endif>{{ $i.' Keys' }}</option>
                                @endfor
                            </select>
                            @error('complexity')
                            <div class="text-muted text-danger"> {{ $errors->first('complexity') }} </div>
                            @enderror
                        </div>
                        <div style="height: 500px;overflow-x: auto;"> 
                            @forelse($widgetItem as $key => $widgets)
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <h4 class="control-label">{{ $key }}:</h4>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" value="{{ $key.'widgets' }}" class="widgets_select_all" data-widgets="{{ $key }}">Select All
                                    </label>
                                    <input type="hidden" name="widgets[{{ $key }}][]" class="all_widgets{{ $key }}">
                                </div>
                                @forelse($widgets as $gender=> $genderBased)
                                    <div class="col-md-12">
                                        <h4 >{{ $gender }}:</h4>
                                        @forelse($genderBased as $widget)
                                        <div class="col-md-3">
                                            <label for="{{ $widget->id }}" >
                                                @if (file_exists(public_path('admin_assets/widgets/'.$widget->id.'.png')))
                                                    <img class="card-img-top" src="{{ asset('admin_assets/widgets/'.$widget->id.'.png') }}" style="width: 100%" >
                                                @else
                                                    <img class="card-img-top" src="{{ asset('admin_assets/images/no_image.png') }}" style="width: 100%" >
                                                @endif
                                            </label>
                                            @php
                                                $widgetIds = [];
                                                if($relicReward->widgets[Str::plural(strtolower($key))] != ""){
                                                    $widgetIds = $relicReward->widgets[Str::plural(strtolower($key))];
                                                }
                                            @endphp
                                            <label class="checkbox-inline">
                                                <input type="checkbox" id="{{ $widget->id }}" class="{{ $key.'widgets' }}" name="widgets[{{ $key }}][]" value="{{ $widget->id }}"  @if(in_array($widget->id, $widgetIds)){{ 'checked' }} @endif>{{ $widget->item_name }}
                                            </label>
                                        </div>
                                        @empty
                                            <label>No Data Found</label>
                                        @endforelse
                                    </div>
                                @empty
                                    <label>No Data Found</label>
                                @endforelse
                            </div>
                            @empty
                                <p>No Data Found</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save</button>
                <button type="button" class="btn btn-danger" id="resetTheForm" onclick="document.getElementById('addRelicForm').reset()">Reset</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    
    $(document).on('submit', '#editRelicRewardForm', function(e) {
        e.preventDefault();
        let url = "{{ route('admin.relicReward.update',$relicReward->id) }}";
        // if(validate()) {
            $.ajax({
                type: "POST",
                url: url,
                data: new FormData(this),
                contentType: false,
                processData: false,
                cache: false,
                success: function(response)
                {
                    if (response.status == true) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = '{{ route('admin.relicReward.index') }}';
                        }, 2000)
                    } else {
                        // toastr.warning('You are not authorized to access this page.');
                        toastr.warning(response.message);
                    }
                },
                error: function(xhr, exception) {
                    let error = JSON.parse(xhr.responseText);
                    toastr.error(error.message);
                }
            });
        // }
    });
    $('#minigames').select2();


    $(document).on('click','.widgets_select_all',function(){
        var checkVal = $(this).val();
        var widget = $(this).attr('data-widgets');
        if ($(this).prop("checked")== true) {
            $('.'+checkVal).prop("checked",true);
            $('.all_widgets'+widget).val('all');
        } else {
            $('.'+checkVal).prop("checked",false);
            $('.all_widgets'+widget).val('no');
        }
    })
</script>
@endsection