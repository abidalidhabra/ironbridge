@section('title','Ironbridge1779 | Maps')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="users_datatablebox">
            <div class="row">
                <div class="col-md-6">
                    <h3>Treasure hunt locations</h3>
                </div>
                <div class="col-md-6 text-right modalbuttonadd">
                    <a href="{{ route('admin.add_location') }}" class="btn btn-info btn-md">Add Location</a>
                </div>
            </div>
        </div>
        <br/>
        <br/>
        <div class="customdatatable_box">
            <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
                <thead>
                    <tr>
                        <th>Sr</th>
                        <th>Place Name</th>
                        <th>Country</th>
                        <th>Province</th>
                        <th>City</th>
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
                order: [[4, 'asc']],
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
                    { data:'country',name:'country' },
                    { data:'province',name:'province' },
                    { data:'city',name:'city' },
                    { data:'map',name:'map'},
                ],

            });
            
        });
    </script>
@endsection