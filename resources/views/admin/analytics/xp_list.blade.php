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
                <h3>XP List</h3>
            </div>
            <div class="col-md-6 text-right modalbuttonadd">
                <a href="{{ route('admin.analyticsMetrics') }}" class="btn btn-info btn-md">Back</a>
            </div>
        </div>
        <div class="row">
            <div class="daterightbox">
                <form method="post" id="userDaterangepickerForm">
                    @csrf
                    <img src="{{ asset('admin_assets/images/datepicker.png') }}">
                    <input type="text" name="user_date" value="" />
                </form>
            </div>
            <div class="refreshbox">
                <a href="javascript:void(0)" id="refresh_user" data-action="refresh" data-date="{{ $data['user_start_date']->format('d M Y').' - '.$data['user_end_date']->format('d M Y') }}"><i class="fa fa-refresh"></i></a>
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
                    <th>XP</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(window).load(function() {
            /* USER DTAE RANGE FILETR */
            var userStartDate =  "{{ $data['user_start_date']->format('d M Y') }}";
            var userEndDate = "{{ $data['user_end_date']->format('d M Y') }}";

            userDaterangepicker();
            function userDaterangepicker(){
                $('input[name="user_date"]').daterangepicker({ 
                    maxDate: new Date(),
                    startDate: userStartDate,
                    endDate: userEndDate,
                    locale: {
                        format: 'DD MMM YYYY',
                    },
                    ranges: {
                     'Today': [moment(), moment()],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                 }
             });
            }

            $('input[name="user_date"]').change(function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

            $('#refresh_user').click(function(e){
                e.preventDefault();
                // $('input[name="user_date"]').val($(this).attr('data-date'));
                table.ajax.reload();
            });


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
                    url: "{{ route('admin.analyticsMetrics.getXPList') }}",
                    data: function ( d ) {
                        d._token = "{{ csrf_token() }}";
                        d.user_date = $('input[name="user_date"]').val();
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
                { data:'xp',name:'xp' },
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