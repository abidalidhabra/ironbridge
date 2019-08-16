@section('title','Ironbridge1779 | User')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="users_datatablebox userstextset row">
            <div class="col-md-6 text-left">
                <h3>Users Participated</h3>
            </div>
            <div class="col-md-6 text-right">
                <select id="filter_hunts">
                    <option value="all">All</option>
                    <option value="challenge">Challenge</option>
                    <option value="normal">Normal</option>
                </select>
            </div>
        </div>
        <div class="customdatatable_box">
            <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
                <thead>
                    <tr>
                        <th>Sr.</th>
                        <th>Hunt Name</th>
                        <th>User Name</th>
                        <th>Fees</th>
                        <th>Clues Progress</th>
                        <th>Distance progress</th>
                        <th>More</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

@endsection

@section('scripts')
    <!-- <script type="text/javascript" src="{{ asset('js/toastr.min.js') }}"></script> -->
    <script type="text/javascript">
        $(document).ready(function() {
            //GET USER LIST
            var table = $('#dataTable').DataTable({
                pageLength: 10,
                processing: true,
                responsive: true,
                serverSide: true,
                order: [[1, 'desc']],
                lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                ajax: {
                    type: "get",
                    url: "{{ route('admin.getUsertParticipatedList') }}",
                    data: function ( d ) {
                        d._token = "{{ csrf_token() }}";
                        d.hunt_mode = $("#filter_hunts option:selected" ).val();
                    },
                    complete:function(){
                        if( $('[data-toggle="tooltip"]').length > 0 )
                            $('[data-toggle="tooltip"]').tooltip();
                    }
                },
                columns:[
                    { data:'DT_RowIndex',name:'_id' },
                    { data:'hunt_name',name:'hunt_name'},
                    { data:'username',name:'username' },
                    { data:'fees',name:'fees' },
                    { data:'clue_progress',name:'clue_progress' },
                    { data:'distance_progress',name:'distance_progress'},
                    { data:'view',name:'view'},
                ],
            });


            //HUNT MODE CHANGE
            $("#filter_hunts").change(function(){
                table.ajax.reload();
            });
            
        });

    </script>
@endsection