@section('title','Ironbridge1779 | Games')
@extends('admin.layouts.admin-app')
@section('styles')
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">      
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Relics List</h3>
            </div>
            <div class="col-md-6 text-right modalbuttonadd">
                <a href="{{ route('admin.analyticsMetrics') }}" class="btn btn-info btn-md">Back</a>
            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr.</th>
                    <th>Name</th>
                    <th>Total Relics</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
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
                    url: "{{ route('admin.analyticsMetrics.getRelicsList') }}",
                    data: function ( d ) {
                        d._token = "{{ csrf_token() }}";
                    },
                    complete:function(){
                        afterfunction();
                        if( $('[data-toggle="tooltip"]').length > 0 )
                            $('[data-toggle="tooltip"]').tooltip();
                    }
                },
                columns:[
                { data:'DT_RowIndex',name:'_id' },
                { data:'name',name:'name'},
                { data:'relics',name:'relics' },
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0,2],
                    }
                ],

            });
        });
    </script>
    @endsection