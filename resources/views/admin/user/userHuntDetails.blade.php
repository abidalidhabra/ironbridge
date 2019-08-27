@section('title','Ironbridge1779 | User')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="users_datatablebox userstextset">
            <div class="row">
                <div class="col-md-6">
                    <h3>Hunt Users Details</h3>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('admin.treasureHunts',$id) }}" class="btn back-btn">Back</a>
                </div>
            </div>
        </div>
        <div class="customdatatable_box">
            <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
                <thead>
                    
                    <tr>
                        <th width="7%">Sr.</th>
                        <th>Time spent</th>
                        <th>Status</th>
                        <th>Game Name</th>
                        <th>Variation Name</th>
                        <th>Revealed at</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $i = 1;
                    ?>
                    @forelse ($huntUserDetail as $user)
                        <tr>
                            <!--  status [tobestart, running, paused, completed]  -->
                            <?php
                                if($user->status == 'tobestart'){
                                    $status = 'Not Started';
                                } elseif ($user->status == 'running' || $user->status == 'paused') {
                                    $status = 'In Progress';
                                } elseif ($user->status == 'completed') {
                                    $status = 'Completed';
                                }

                            ?>
                            <td>{{ $i }}</td>
                            <td>{{ $user->finished_in }}</td>
                            <td>{{ $status }}</td>
                            <td>{{ $user->game->name }}</td>
                            <td>{{ $user->game_variation->variation_name }}</td>
                            <td>{{ ($user->revealed_at)?$user->revealed_at->format('d-M-Y @ h:i A'):'-' }}</td>
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