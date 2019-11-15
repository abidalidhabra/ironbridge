@section('title','Ironbridge1779 | Agent Levels')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="">               
            <div class="col-md-12 text-right">
                <a href="{{ route('admin.avatar-agent-levels.index') }}" class="btn back-btn">Back</a>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <h3>Add Avatar Agent Level</h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <form method="POST" id="addRelicRewardForm">
            @csrf
            <div class="modal-body padboxset">
                <div class="modalbodysetbox">
                    <div class="addrehcover">
                        <div class="form-group @error('agent_level') has-error @enderror">
                            <label class="control-label">Agent Level:</label>
                            <select name="agent_level" class="form-control">
                                <option>Select Agent Level</option>
                                @forelse($agent_complementary as $agent)
                                    <option value="{{ $agent->agent_level }}">{{ $agent->agent_level }}</option>
                                @empty
                                @endforelse
                            </select>
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
                                            <label class="checkbox-inline">
                                                <input type="checkbox" id="{{ $widget->id }}" class="{{ $key.'widgets' }}" name="widgets[{{ $key }}][]" value="{{ $widget->id }}">{{ $widget->item_name }}
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
                <!-- <button type="button" class="btn btn-danger" id="resetTheForm" onclick="document.getElementById('addRelicForm').reset()">Reset</button> -->
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    
    $(document).on('submit', '#addRelicRewardForm', function(e) {
        e.preventDefault();
        let url = "{{ route('admin.avatar-agent-levels.store') }}";
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
                            window.location.href = '{{ route('admin.avatar-agent-levels.index') }}';
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