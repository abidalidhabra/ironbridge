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
            <h3>Practice Game Users</h3>
            
            <div class="innerdatingactivity1">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Game Name</th>
                            <th>Completion Times</th>
                            <th>Completed Date</th>
                            <th>Unlocked Date</th>
                            <th>Favourite</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @forelse($practiceGames as $practice)
                            <tr>
                                <th>{{ $i }}</th>
                                <th>{{ $practice->game->name }}</th>
                                <th>{{ $practice->completion_times }}</th>
                                <th>{{ ($practice->completed_at)?$practice->completed_at->format('d-M-Y @ h:i A'):'-' }}</th>
                                <th>{{ ($practice->unlocked_at)?$practice->unlocked_at->format('d-M-Y @ h:i A'):'-' }}</th>
                                <th>
                                    @if($practice->favourite == "true")
                                        <label class="label label-success">Favourite</label>
                                    @elseif($practice->favourite == "false")
                                        <label class="label label-danger">Not favourite</label>
                                    @else
                                        -
                                    @endif
                                </th>
                            </tr>
                            @php $i++; @endphp
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.table').DataTable({
                "order": [],
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