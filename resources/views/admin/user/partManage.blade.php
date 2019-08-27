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
                            <th>Completed Date</th>
                            <th>Piece Collected</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @forelse($practiceGames as $practice)
                            <tr>
                                <th>{{ $i }}</th>
                                <th>{{ $practice->game->name }}</th>
                                <th>{{ ($practice->completed_at)?$practice->completed_at->format('d-m-Y'):'-' }}</th>
                                <th>{{ ($practice->piece_collected)?'Collected':'Not Collected' }}</th>
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