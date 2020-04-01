@section('title','Ironbridge1779 | Hunt Statistics')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="customdatatable_box" id="formContainer">
        <div class="users_datatablebox">
            <h3>Distance and XP</h3>
        </div>
        <form method="POST" id="updateHuntStatisticForm" class="appstbboxcover" action="{{ route('admin.app.settings.update') }}">
            @csrf
            @method('PUT')
            <div class="appstbboxin">
                <div class="row">
                    
                    <div class="col-md-3">
                        <h4>Power Charging Station</h4>
                        <div class="form-group">
                            <label class="control-label">Power Ratio:</label>
                            <input type="number" name="power_ratio" class="form-control" value="{{ $huntStatistic->power_ratio }}" placeholder="Enter the power ratio">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Boost Validity Till (Seconds):</label>
                            <input type="number" name="boost_power_till" class="form-control" value="{{ $huntStatistic->boost_power_till }}" placeholder="Enter the boot validity till">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <h4 class="text-center">Bonus Nodes</h4>
                        <div class="form-group">
                            <label class="control-label">
                                Gold:
                            </label>
                            <input type="number" name="gold" class="form-control" placeholder="Enter the gold" value="{{ $huntStatistic->gold }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Skeleton keys:</label>
                            <input type="number" name="skeleton_keys" class="form-control" placeholder="Enter the skeleton keys" value="{{ $huntStatistic->skeleton_keys }}">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <h4 class="text-center">Chests Nodes</h4>
                        <div class="row">
                            <div class="form-group">
                                <label class="control-label" data-toggle="tooltip" data-title="This indicates the golds to be cut if user want to change the chest minigame.">
                                    Gold: <i class="fa fa-question-circle"></i>
                                </label>
                                <input type="number" name="chest[golds_to_skip_mg]" class="form-control" placeholder="Enter the golds" value="{{ $huntStatistic->chest['golds_to_skip_mg'] }}">
                            </div>
                            <div class="form-group">
                                <label class="control-label" data-toggle="tooltip" data-title="This indicates the skeleton keys to be cut if user want to skip the chest.">
                                    Skeleton keys: <i class="fa fa-question-circle"></i>
                                </label>
                                <input type="number" name="chest[skeleton_keys_to_skip]" class="form-control" placeholder="Enter the skeleton keys" value="{{ $huntStatistic->chest['skeleton_keys_to_skip'] }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <h4 class="text-center">Skeleton Nodes</h4>
                        <div class="form-group">
                            <label class="control-label" data-toggle="tooltip" data-title="This indicate the skeleton keys to give user on finding skeleton node.">
                                Skeleton keys: <i class="fa fa-question-circle"></i>
                            </label>
                            <input type="number" name="skeleton_keys_for_node" class="form-control" placeholder="Enter the power cool-down period" value="{{ $huntStatistic->skeleton_keys_for_node }}">
                        </div>
                    </div>
                    
                </div>
               
                <div class="row">
                    <div class="col-md-3">
                        <h4>Refreshable Distance</h4>
                        <div class="form-group">
                            <label class="control-label" data-toggle="tooltip" data-title="Re-load the api after reaching to this distance. (In meter)">Random Hunt: <i class="fa fa-question-circle"></i></label>
                            <input type="number" name="refreshable_random_hunt" class="form-control" placeholder="Enter the random hunt" value="{{ ($huntStatistic->refreshable_distances)?$huntStatistic->refreshable_distances['random_hunt']:'' }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label" data-toggle="tooltip" data-title="Re-load the api after reaching to this distance. (In meter)">Nodes: <i class="fa fa-question-circle"></i></label>
                            <input type="number" name="nodes" class="form-control" placeholder="Enter the nodes" value="{{ ($huntStatistic->refreshable_distances)?$huntStatistic->refreshable_distances['nodes']:'' }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <h4>Distances</h4>
                        <div class="form-group">
                            <label class="control-label">Random hunt distance:
                                <a data-toggle="tooltip" title="" data-placement="right" data-original-title="Distance should be in meter">?</a>
                            </label>
                            <input type="number" name="distances_random_hunt" class="form-control" placeholder="Enter the random hunt" value="{{ ($huntStatistic->distances)?$huntStatistic->distances['random_hunt']:'' }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Relic hunt distance:
                                <a data-toggle="tooltip" title="" data-placement="right" data-original-title="Distance should be in meter">?</a>
                            </label>
                            <input type="number" name="relic" class="form-control" placeholder="Enter the relic" value="{{ ($huntStatistic->distances)?$huntStatistic->distances['relic']:'' }}">
                        </div>
                    </div>

                    <div class="col-md-6 borderize-container">
                        <h4 class="text-center">Freeze time</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" data-toggle="tooltip" data-title="Freez Till. (seconds)">
                                        Powe-up: <i class="fa fa-question-circle"></i>
                                    </label>
                                    <input type="number" name="power" class="form-control" placeholder="Enter the power cool-down period" value="{{ ($huntStatistic->freeze_till)?$huntStatistic->freeze_till['power']:'' }}">
                                </div>
                                <div class="form-group">
                                    <label class="control-label" data-toggle="tooltip" data-title="Freez Till. (seconds)">
                                        MGC: <i class="fa fa-question-circle"></i>
                                    </label>
                                    <input type="number" name="mgc" class="form-control" placeholder="Enter the mgc cool-down period" value="{{ ($huntStatistic->freeze_till)?$huntStatistic->freeze_till['mgc']:'' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" data-toggle="tooltip" data-title="Freez Till. (seconds)">
                                        Chest: <i class="fa fa-question-circle"></i>
                                    </label>
                                    <input type="number" name="freeze_till_chest" class="form-control" placeholder="Enter the chest cool-down period" value="{{ ($huntStatistic->freeze_till)?$huntStatistic->freeze_till['chest']:'' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
              <button type="submit" class="btn btn-success">Save</button>
          </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')

<script>
    $('[data-toggle="tooltip"]').tooltip();
    $(document).on('submit', '#updateHuntStatisticForm', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '{{ route('admin.hunt_statistics.update',$huntStatistic->id) }}',
            data: $(this).serialize(),
            dataType: 'json',
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
</script>
@endsection