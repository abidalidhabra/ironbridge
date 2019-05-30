@section('title','ironbridge1779 | Maps')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="users_datatablebox">
            <h3>Treasure hunt locations</h3>
        </div>
        <br/>
        <br/>
        <div class="customdatatable_box">
            <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
                <thead>
                    <tr>
                        <th>Sr</th>
                        <th>Place Name</th>
                        <th>City</th>
                        <th>Province</th>
                        <th>Open in Map</th>
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
                // order: [[1, 'desc']],
                lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                ajax: {
                    type: "get",
                    url: "{{ route('admin.getMaps') }}",
                    data: function ( d ) {
                        d._token = "{{ csrf_token() }}";
                    },
                    complete:function(){
                        if( $('[data-toggle="tooltip"]').length > 0 )
                            $('[data-toggle="tooltip"]').tooltip();
                    }
                },
                columns:[
                    { data:'DT_RowIndex',name:'_id' },
                    { data:'place_name',name:'place_name' },
                    { data:'city',name:'city' },
                    { data:'province',name:'province' },
                    { data:'map',name:'map'},
                ],

            });
            
        });
    </script>
@endsection