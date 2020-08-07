@section('title','Ironbridge1779 | Chest Inverntory')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">
    <!-- <div class="backbtn">
        <a href="{{ route('admin.userList') }}">Back</a>
    </div> -->
    <div class="users_datatablebox">
        <div class="row">
            <div class="backbtn">
                <a href="{{ route('admin.userList') }}">Back</a>
            </div>
            <div class="col-md-6">
                <h3>Chest Inverntory</h3>
            </div>
        </div>
    </div>
    <!-- <div class="customdatatable_box"> -->
    <div class="verified_detlisbox">
        <ul>
            <div class="col-md-6">
                <li>
                    <img src="{{ asset('admin_assets/svg/news.svg') }}">                    
                    <h3>Chest Bucket Size</h3>
                    <p>{{ (isset($chests['capacity']))?$chests['capacity']:0 }}</p>
                </li>
            </div>
            <div class="col-md-6">
                <li>
                    <img src="{{ asset('admin_assets/svg/news.svg') }}">                    
                    <h3>Remaining Chests</h3>
                    <p>{{ isset($chests['remaining'])?$chests['remaining']:0 }}</p>
                </li>
            </div>
            <div class="col-md-6">
                <li>
                    <img src="{{ asset('admin_assets/svg/news.svg') }}">                    
                    <h3>Collected Chests</h3>
                    <p>{{ isset($chests['collected'])?$chests['collected']:0 }}</p>
                </li>
            </div>
            <div class="col-md-6">
                <li>
                    <img src="{{ asset('admin_assets/svg/news.svg') }}">                    
                    <h3>Upcoming Minigame</h3>
                    <p>{{ $chests['mini_game'] }}</p>
                </li>
            </div>
        </ul>
    </div>
</div>
@endsection

@section('scripts')
    
@endsection