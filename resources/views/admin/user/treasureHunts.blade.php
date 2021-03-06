@section('title','Ironbridge1779 | User')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="backbtn">
            <a href="{{ route('admin.userList') }}">Back</a>
        </div>
        <div class="srfrdbox">
            <h3>Treasure Hunts</h3>
            <div class="custredbtn">
                <h4>Status : </h4>
                <div class="selection">
                    <input id="progress" value="progress" name="status" type="radio">
                    <label for="progress">PROGRESS</label>
                </div>
                <div class="selection">
                    <input id="completed" value="completed" name="status" type="radio">
                    <label for="completed">COMPLETED</label>
                </div>
                <div class="selection">
                    <input id="terminated" value="terminated" name="status" type="radio">
                    <label for="terminated">TERMINATED</label>
                </div>
            </div>
            <div class="custredbtn">
                <h4>Type : </h4>
                <div class="selection">
                    <input id="random" value="random" name="type" type="radio">
                    <label for="random">RANDOM</label>
                </div>
                <div class="selection">
                    <input id="relic" value="relic" name="type" type="radio">
                    <label for="relic">RELIC</label>
                </div>
            </div>
        </div>
        <div class="customdatatable_box">
            <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
                <thead>
                    <tr>
                        <th width="10%">Sr no</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Clue Progress</th>
                        <th>Relic</th>
                        <!-- <th>More</th> -->
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
                order: [],
                lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                ajax: {
                    type: "get",
                    url: "{{ route('admin.getTreasureHunts') }}",
                    data: function ( d ) {
                        d._token = "{{ csrf_token() }}";
                        d.user_id = '{{ $id }}';
                        d.status = $("input[name='status']:checked").val();
                        d.type = $("input[name='type']:checked").val();
                    },
                    complete:function(){
                        if( $('[data-toggle="tooltip"]').length > 0 )
                            $('[data-toggle="tooltip"]').tooltip();
                    }
                },
                columns:[
                    { data:'DT_RowIndex',name:'_id' },
                    { data:'created_at',name:'created_at'},
                    { data:'status',name:'status' },
                    // { data:'fees',name:'fees' },
                    { data:'clue_progress',name:'clue_progress' },
                    { data:'relic',name:'relic' },
                    // { data:'distance_progress',name:'distance_progress'},
                    // { data:'view',name:'view'},
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0],
                    }
                ],
            });


            //HUNT MODE CHANGE
            $("input[name='status']").change(function(){
                table.ajax.reload();
            });
            
            //TYPE
            $("input[name='type']").change(function(){
                table.ajax.reload();
            });
        });

    </script>
@endsection