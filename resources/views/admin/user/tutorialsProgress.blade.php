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
            <h3>Tutorials Progress</h3>
            <div class="innerdatingactivity">
                
                @forelse($tutorials  as $index => $tutorial)
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>{{ ucfirst(str_replace('_',' ',$index)) }}</p> 
                    </div>
                    <div class="swoped_detlisright">
                        @if($tutorial)
                        <span>
                            <i class="fa fa-check-circle" style="color: #0D8441;"></i>
                        </span>
                        @else
                        <span>
                            <i class="fa fa-times-circle" style="color: #d6302b;"></i>
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                    <div class="swoped_detlisbox">
                        <div class="swoped_detlisleft">
                           <p>No data found</p> 
                        </div>
                        <div class="swoped_detlisright">
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('scripts')

@endsection