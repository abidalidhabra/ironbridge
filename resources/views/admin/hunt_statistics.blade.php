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
                    <div class="col-md-4">
                        <h4>Charging Station</h4>
                        <div class="form-group">
                            <label class="control-label">Power Ratio:</label>
                            <input type="number" name="power_ratio" class="form-control" value="{{ $huntStatistic->power_ratio }}" placeholder="Enter the power ratio">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Boost Validity Till (Seconds):</label>
                            <input type="number" name="boost_power_till" class="form-control" value="{{ $huntStatistic->boost_power_till }}" placeholder="Enter the boot validity till">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h4>Challenge Nodes</h4>
                        <div class="form-group">
                            <label class="control-label">Gold:</label>
                            <input type="number" name="gold" class="form-control" placeholder="Enter the gold" value="{{ $huntStatistic->gold }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Skeleton keys:</label>
                            <input type="number" name="skeleton_keys" class="form-control" placeholder="Enter the skeleton keys" value="{{ $huntStatistic->skeleton_keys }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h4>Refreshable Distance</h4>
                        <div class="form-group">
                            <label class="control-label">Refreshable distance of random hunt:
                                <a data-toggle="tooltip" title="" data-placement="right" data-original-title="Distance should be in meter">?</a>
                            </label>
                            <input type="number" name="refreshable_random_hunt" class="form-control" placeholder="Enter the random hunt" value="{{ ($huntStatistic->refreshable_distances)?$huntStatistic->refreshable_distances['random_hunt']:'' }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Refreshable distance of nodes:
                                <a data-toggle="tooltip" title="" data-placement="right" data-original-title="Distance should be in meter">?</a>
                            </label>
                            <input type="number" name="nodes" class="form-control" placeholder="Enter the nodes" value="{{ ($huntStatistic->refreshable_distances)?$huntStatistic->refreshable_distances['nodes']:'' }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
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
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h4>Freeze time</h4>
                        <div class="form-group">
                            <label class="control-label">Powe-up freeze time:
                                <!-- <a data-toggle="tooltip" title="" data-placement="right" data-original-title="Distance should be in meter">?</a> -->
                            </label>
                            <input type="number" name="power" class="form-control" placeholder="Enter the power" value="{{ ($huntStatistic->freeze_till)?$huntStatistic->freeze_till['power']:'' }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">MGC Freeze time:
                                <!-- <a data-toggle="tooltip" title="" data-placement="right" data-original-title="Distance should be in meter">?</a> -->
                            </label>
                            <input type="number" name="mgc" class="form-control" placeholder="Enter the mgc" value="{{ ($huntStatistic->freeze_till)?$huntStatistic->freeze_till['mgc']:'' }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h4>Chest Settings</h4>
                        <div class="form-group">
                            <label class="control-label">Fix XP to provide on chest opening:
                            </label>
                            <input type="number" name="chest_xp" class="form-control" placeholder="Enter the XP" value="{{ $huntStatistic->chest_xp }}">
                        </div>

                        <div class="form-group">
                            <label class="control-label">Golds to cut if user change MG for chest:
                            </label>
                            <input type="number" name="mg_change_charge" class="form-control" placeholder="Enter the Gold amount to cut if user change MG for chest" value="{{ $huntStatistic->mg_change_charge }}">
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