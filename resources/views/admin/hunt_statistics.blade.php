@section('title','Ironbridge1779 | Hunt Statistics')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="customdatatable_box" id="formContainer">
        <div class="users_datatablebox">
            <h3>Hunt Statistics</h3>
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
                            <label class="control-label">Boot Validity Till (Seconds):</label>
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
              <button type="submit" class="btn btn-success">Save</button>
          </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')

<script>
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