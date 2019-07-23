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
            <h3>Avatar Details</h3>
            <div class="innerdatingactivity">
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Name</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <span>{{ $avatar->name }}</span>
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Gender</p> 
                    </div>
                    <div class="swoped_detlisright">
                        <p>{{ ($avatar->gender)?$avatar->gender:'-' }}</p>
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Skin Colors</p> 
                    </div>
                    <div class="swoped_detlisright">
                        @forelse($avatar->skin_colors as $skinColor)
                            <div class="px20_20" style="background: {{ $skinColor }}"></div>
                        @empty
                        @endforelse
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Hairs Colors</p> 
                    </div>
                    <div class="swoped_detlisright">
                        @forelse($avatar->hairs_colors as $hairsColor)
                            <div class="px20_20" style="background: {{ $hairsColor }}"></div>
                        @empty
                        @endforelse
                    </div>
                </div>
                <div class="swoped_detlisbox">
                    <div class="swoped_detlisleft">
                       <p>Eyes Colors</p> 
                    </div>
                    <div class="swoped_detlisright">
                        @forelse($avatar->eyes_colors as $eyesColor)
                            <div class="px20_20" style="background: {{ $eyesColor }}"></div>
                        @empty
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="avtardetailbox">
            @forelse($widgetItem as $key => $widgetlist)
                <h4>{{ $key }}</h4>
                @forelse($widgetlist as $widget)
                <div class="avtarimgtextiner">
                    <img class="card-img-top" src="{{ asset('admin_assets/images/FullDressup.png') }}">
                    <div class="card-body">
                        <h5 class="card-title">Gold Price ${{ $widget->gold_price }}</h5>
                        <p class="card-text">{{ $widget->id}}</p>
                    </div>
                </div>
                @empty
                @endforelse
            @empty
            @endforelse

            
        </div>
    </div>

@endsection

@section('scripts')
    
@endsection