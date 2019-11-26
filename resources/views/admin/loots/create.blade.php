@section('title','Ironbridge1779 | Loot')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="">               
            <div class="col-md-12 text-right">
                <a href="{{ route('admin.loots.index') }}" class="btn back-btn">Back</a>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <h3>Add Loot</h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <form method="POST" id="addLootForm" action="{{ route('admin.seasons.store') }}">
            @csrf
            <div class="modal-body padboxset">
                <div class="modalbodysetbox">
                    <div class="addrehcover">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">Please select status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Relics</label>
                                <select name="relics[]" class="form-control" id="relics" multiple>
                                    <!-- <option value="">Please select relics</option> -->
                                    @forelse($relics as $relic)
                                        <option value="{{ $relic->id }}">{{ $relic->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="golds">
                            <h3>Gold</h3>
                            <input type="checkbox" name="gold_check" value="true"> Gold<br>
                            @include('admin.loots.reward.gold', ['index'=> 0])
                        </div>
                        <div class="skeleton_keys">
                            <h3>Skeleton Keys</h3>
                            <input type="checkbox" name="skeleton_key_check" value="true"> Skeleton Keys<br>
                            @include('admin.loots.reward.skeleton', ['index'=> 0])
                        </div>
                        <div class="skeleton_golds">
                            <h3>Skeleton Golds</h3>
                            <input type="checkbox" name="skeleton_gold_check" value="true"> Skeleton Golds<br>
                            @include('admin.loots.reward.skeleton_gold', ['index'=> 0])
                        </div>
                        <div class="avatars">
                            <h3>Avatars</h3>
                            <input type="checkbox" name="avatar_check" value="true"> Avatars<br>
                            @include('admin.loots.reward.avatar', ['index'=> 0])
                        </div>
                            <!-- @include('admin.loots.reward.skeleton_gold', ['index'=> 0]) -->
                            <input type="hidden" id="last-token" value="0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.loots.scripts.loot-script')
<script>
    
    $(document).on('submit', '#addLootForm', function(e) {
        e.preventDefault();
        let url = "{{ route('admin.loots.store') }}";
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
                            window.location.href = '{{ route('admin.loots.index') }}';
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
    $('#relics').select2();
</script>
@endsection