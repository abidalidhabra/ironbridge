@section('title','Ironbridge1779 | Practice Game')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="">               
            <div class="col-md-12 text-right">
                <a href="{{ route('admin.practiceGame.index') }}" class="btn back-btn">Back</a>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <h3>Details Practice Game</h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <form method="POST" id="addPracticeGameForm" action="{{ route('admin.practiceGame.update', $practiceGame->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-body padboxset">
                <div class="modalbodysetbox">
                    <div class="addrehcover">
                        <div class="form-group">
                            
                            <p><label>Game: </label>{{ $practiceGame->game->name }}</p>
                        </div>

                        @if($practiceGame->game_id == '5b0e306951b2010ec820fb4f' || $practiceGame->game_id == '5b0e304b51b2010ec820fb4e')
                            <div id="photo_section" class="imageslibbox1" >
                                
                                <ul>
                                @forelse($practiceGame->variation_images as $variation_image)
                                    <li>
                                    <!-- <div class="col-md-3"> -->
                                        
                                        <div class="photosboxset">
                                            <a data-fancybox="gallery" href="{{ $variation_image }}">
                                                <img src="{{ $variation_image }}">
                                            </a>
                                        </div>
                                    <!-- </div> -->
                                    </li>
                                @empty
                                @endforelse
                                </ul>
                            </div>
                        @endif
                        <div class="clues">
                            <label>Targets</label>
                            <div class="col-md-12">
                            </div>
                            @forelse($practiceGame->targets as $index=> $targets)
                                <div class="col-md-3">
                                    <p><label>Srore</label> {{ (isset($targets['score'])?$targets['score']:'') }}</p>
                                    <p><label>Time</label> {{ (isset($targets['time'])?$targets['time']:'') }}</p>
                                    <p><label>XP</label> {{ (isset($targets['xp'])?$targets['xp']:'') }}</p>
                                </div>
                            @empty
                            @endforelse
                        </div>                      
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
                            

@section('scripts')


@endsection