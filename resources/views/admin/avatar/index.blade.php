@section('title','Ironbridge1779 | Avatar')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="users_datatablebox userstextset">
            <h3>Avatars</h3>
        </div>
        <div class="customdatatable_box">
            <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
                <thead>
                    <tr>
                        <th width="7%">Sr.</th>
                        <!-- <th>Name</th> -->
                        <th>Gender</th>
                        <th>Tops</th>
                        <th>Feets</th>
                        <th>Hats</th>
                        <th>Outfits</th>
                        <th>Bottom</th>
                        <th width="10%">Action</th>
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
                pageLength: 50,
                processing: true,
                responsive: true,
                serverSide: true,
                order: [[1, 'desc']],
                lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                ajax: {
                    type: "get",
                    url: "{{ route('admin.getAvatarsList') }}",
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
                    // { data:'name',name:'name' },
                    { data:'gender',name:'gender' },
                    { data:'tops',name:'tops' },
                    { data:'feets',name:'feets' },
                    { data:'hats',name:'hats' },
                    { data:'outfits',name:'outfits' },
                    { data:'bottom',name:'bottom' },
                    { data:'action',name:'action'},
                ],

            });
            
        });
    </script>
@endsection