<style>
    .badge-danger{
        background-color: #dc3545;
    }
    .badge-success{
        background-color: #28a745;
    }
</style>
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Pre-Scheduled Notifications</h3>
            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="notificationsTable">
            <thead>
                <tr>
                    <th width="7%">Sr.</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Sent At (UTC)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

<script type="text/javascript">
    $(document).ready(function() {
            var notificationsTable = $('#notificationsTable').DataTable({
                pageLength: 10,
                processing: true,
                responsive: true,
                serverSide: true,
                searching : true,
                order: [],
                lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
                ajax: {
                    type: "GET",
                    url: "{{ route('admin.event-notifications.list') }}",
                    data: function ( d ) {
                        d._token = "{{ csrf_token() }}";
                    },
                    complete:function(){

                        if( $('[data-action="delete"]').length > 0 )
                            initializeDeletePopup();

                        if( $('[data-toggle="tooltip"]').length > 0 )
                            $('[data-toggle="tooltip"]').tooltip();
                    }
                },
                columns:[
                    { data:'DT_RowIndex',name:'_id' },
                    { data:'title',name:'title'},
                    { data:'message',name:'message' },
                    { data:'send_at',name:'send_at' },
                    { data:'status',name:'status' },
                    { data:'action',name:'action' }
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0,1,2,3,4],
                    }
                ]
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
                                toastr.success(response.message);
                                notificationsTable.draw();
                            },
                            error: function(xhr, exception) {
                                toastr.error(errHandling(xhr, exception));
                            }
                        });
                    }
                }); 
            }
        });

    </script>
