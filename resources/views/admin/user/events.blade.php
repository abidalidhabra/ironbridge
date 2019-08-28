@section('title','Ironbridge1779 | User')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="datingactivity_box">
            <div class="backbtn">
                <a href="{{ route('admin.userList') }}">Back</a>
            </div>
            <h3>Events</h3>
            <div class="customdatatable_box">
                <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
                    <thead>
                        
                        <tr>
                            <th width="7%">Sr.</th>
                            <th>Event Name</th>
                            <th>Completed Date</th>
                            <th>Status</th>
                            <th>Event City</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i = 1;
                        ?>
                        @forelse ($eventsUser as $user)
                            <tr>
                                <!--  status [tobestart, running, paused, completed]  -->
                                <?php
                                    if($user->status == 'tobestart'){
                                        $status = 'Not Started';
                                    } elseif ($user->status == 'running' || $user->status == 'paused') {
                                        $status = ucfirst($user->status);
                                    }
                                ?>
                                <td>{{ $i }}</td>
                                <td>{{ $user->event->name }}</td>
                                <td>{{ ($user->completed_at != '')?$user->completed_at->format('d-M-Y @ h:i A'):'-' }}</td>
                                <td>{{ $status }}</td>
                                <td>{{ $user->event->city->name }}</td>
                                <td>{{ ($user->created_at)?$user->created_at->format('d-M-Y @ h:i A'):'-' }}</td>
                            </tr>
                            @php $i++ @endphp 
                        @empty
                            <tr>
                                <td valign="top" colspan="6" class="dataTables_empty">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <!-- <script type="text/javascript" src="{{ asset('js/toastr.min.js') }}"></script> -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable({
                order:[],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [0],
                    }
                ],
            });
        });
    </script>
@endsection