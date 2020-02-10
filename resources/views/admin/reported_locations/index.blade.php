@section('title','Ironbridge1779 | Reported Locations')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Reported Google Locations</h3>
            </div>
        </div>
    </div>
    <div class="verified_detlisbox">
            <ul>
                <h3 class="text-center">Google Reported Locations Statistics</h3>
                <div class="col-md-6">
                    <li>
                        <img src="{{ asset('admin_assets/svg/news.svg') }}">
                        <a href="javascript:void(0)">
                            <h3>{{ $totalSubmitted }}</h3>
                            <p>Total Submission</p>
                        </a>
                    </li>
                </div>
                <div class="col-md-6">
                    <li>
                        <img src="{{ asset('admin_assets/svg/news.svg') }}">
                        <a href="javascript:void(0)">
                            <h3>{{ $totalSubmittedToGoogle }}</h3>
                            <p>Total Submission to Google</p>
                        </a>
                    </li>
                </div>
                <div class="col-md-6">
                    <li>
                        <img class="rotimg" src="{{ asset('admin_assets/svg/earth.svg') }}">
                        <button id="submitReportsToGoogle" class="ibbtn">SEND</button>
                        <p>Send the batch to Google</p>
                    </li>
                </div>
                <div class="col-md-6">
                    <li>
                        <img src="{{ asset('admin_assets/svg/news.svg') }}">
                        <input type="text" class="form-controls" name="reported_loc_count" value="{{$huntStatistics->reported_loc_count}}">
                        <button id="updateBunchSize" class="ibbtn">UPDATE</button>
                        <p>Bunch Size</p>
                    </li>
                </div>
            </ul>
        </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="reported_locations">
            <thead>
                <tr>
                    <th>Sr.</th>
                    <th>Date</th>
                    <th>Location Name</th>
                    <th>Reasons</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var table = $('#reported_locations').DataTable({
                    pageLength: 10,
                    processing: true,
                    responsive: true,
                    serverSide: true,
                    order: [],
                    lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                    ajax: {
                        type: "get",
                        url: "{{ route('admin.reported-locations.list') }}",
                        data: function ( d ) {
                            d._token = "{{ csrf_token() }}";
                        },
                        complete:function(){
                            initializeDeletePopup();
                            if( $('[data-toggle="tooltip"]').length > 0 )
                                $('[data-toggle="tooltip"]').tooltip();
                        }
                    },
                    columns:[
                        { data:'DT_RowIndex',name:'_id' },
                        { data:'created_at',name:'created_at'},
                        { data:'locationName',name:'locationName'},
                        { data:'reasons',name:'reasons'},
                        { data:'reasonDetails',name:'reasonDetails'},
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [0],
                    }],
                });

    function initializeDeletePopup() {
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
                            table.ajax.reload();
                        } else {
                            toastr.warning(response.message);
                        }
                    },
                    error: function(xhr, exception) {
                        let error = JSON.parse(xhr.responseText);
                        toastr.error(error.message);
                    }
                });
            }
        }); 
    }

    $(document).on('click', '#submitReportsToGoogle',function() {
        let that = $(this);
        $.ajax({
            type: "POST",
            url: "{{ route('admin.reported-locations.submit') }}",
            beforeSend: function() {
                that.attr('disabled', true);
            },
            success: function(response){
                toastr.success(response.message);
                table.ajax.reload();
            },
            error: function(xhr, exception) {
                let error = JSON.parse(xhr.responseText);
                toastr.error(error.message);
            },
            complete: function() {
                that.attr('disabled', false);
            }
        });
    });    

    $(document).on('click', '#updateBunchSize',function() {
        let that = $(this);
        $.ajax({
            type: "POST",
            data: {reported_loc_count: $('input[name=reported_loc_count]').val()},
            url: "{{ route('admin.reported-locations.updateIt') }}",
            beforeSend: function() {
                that.attr('disabled', true);
            },
            success: function(response){
                toastr.success(response.message);
            },
            error: function(xhr, exception) {
                let error = JSON.parse(xhr.responseText);
                toastr.error(error.message);
            },
            complete: function() {
                that.attr('disabled', false);
            }
        });
    });
</script>
@endsection