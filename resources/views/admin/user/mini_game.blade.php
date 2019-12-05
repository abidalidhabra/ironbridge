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
            <h3>Mini-Games Statistics</h3>
            <div class="innerdatingactivity">
                <table class="table">
                    <thead>
                      <tr>
                        <th>Game</th>
                        <th>Completion <i class="fa text-success fa-check"></i></th>
                        <th>Highest Score <i class="fa text-success fa-arrow-up"></i></th>
                      </tr>
                    </thead>
                    <tbody>
                        @forelse($games as $game)
                      <tr>
                        <td>{{ $game->name }}</td>
                        <td>{{ $game->completion_times }}</td>
                        <td>{{ $game->highest_score }}</td>
                      </tr>
                      @empty
                      @endforelse
                    </tbody>
                  </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection