@section('title','Ironbridge1779 | Mini Games XP/Score')
@extends('admin.layouts.admin-app')
@section('styles')
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">      
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Mini Games XP/Score</h3>
            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th width="7%">Sr.</th>
                    <th>Game</th>
                    <th>Total Targets</th>
                    @if(auth()->user()->hasPermissionTo('Edit Practice Games'))
                    <th width="5%">Action</th>
                    @endif
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
                    url: "{{ route('admin.GetPracticeGameList') }}",
                    data: function ( d ) {
                        d._token = "{{ csrf_token() }}";
                    },
                    complete:function(){
                        // afterfunction();
                        if( $('[data-toggle="tooltip"]').length > 0 )
                            $('[data-toggle="tooltip"]').tooltip();
                    }
                },
                columns:[
                { data:'DT_RowIndex',name:'_id' },
                { data:'game',name:'game'},
                { data:'targets',name:'targets' },
                @if(auth()->user()->hasPermissionTo('Edit Practice Games'))
                    { data:'action',name:'action' },
                @endif
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0,3],
                    }
                ],
            });
        });
    </script>
    @endsection