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
                    <h3>Edit Practice Game</h3>
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
                            <label>Game:</label>
                            <select name="game_id" class="form-control" disabled>
                                <option value="">Select Game</option>
                                @forelse($games as $game)
                                    <option value="{{ $game->id }}" {{ ($practiceGame->game_id == $game->id)?'selected': '' }}>{{ $game->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>

                        @if($practiceGame->game_id == '5b0e306951b2010ec820fb4f' || $practiceGame->game_id == '5b0e304b51b2010ec820fb4e')
                            <div id="photo_section{{$game->id}}" class="imageslibbox1" >
                                <div class="form-group">
                                    <label class="form-label">Variation Image <small class="hidden image_hit">must be 2000*1440 dimension </small></label>
                                    <input type="file" name="variation_image[]" class="form-control variation_image" multiple>
                                </div>
                                <ul>
                                @forelse($practiceGame->variation_images as $variation_image)
                                    <li>
                                    <!-- <div class="col-md-3"> -->
                                        <div class="closeicon">
                                            <a target="_blank" href="javascript:void(0)" class="deleteImage" data-action="delete" data-id="{{ $practiceGame->id }}"  data-image="{{ $variation_image }}">
                                                <img src="{{ asset('admin_assets/svg/close.svg') }}">
                                            </a>
                                        </div>
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
                            @forelse($practiceGame->targets as $index=> $targets)
                                @php $lastIndex = $index; @endphp
                                @include('admin.game.practice.targets.edit', ['index'=> $index,'targets'=>$targets,'last'=> $loop->last])

                            @empty
                                <h4 class="text-danger">No clue found in this relic.</h4>
                            @endforelse
                            <input type="hidden" id="last-token" value="{{ $lastIndex ?? 0 }}">
                        </div>                      
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save</button>
                <button type="button" class="btn btn-danger" id="resetTheForm" onclick="document.getElementById('addPracticeGameForm').reset()">Reset</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.game.practice.targets.scripts.target-script')

<script>
    $(document).on('submit', '#addPracticeGameForm', function(e) {
        e.preventDefault();
        if(validate()) {
            $.ajax({
                type: "POST",
                url: '{{ route('admin.practiceGame.update', $practiceGame->id) }}',
                data: new FormData(this),
                contentType: false,
                processData: false,
                cache: false,
                beforeSend: function() {    
                    $('body').css('opacity','0.5');
                },
                success: function(response)
                {
                    $('body').css('opacity','1');
                    if (response.status == true) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = '{{ route('admin.practiceGame.index') }}';
                        }, 2000)
                    } else {
                        toastr.warning(response.message);
                    }
                },
                error: function(xhr, exception) {
                    let error = JSON.parse(xhr.responseText);
                    toastr.error(error.message);
                }
            });
        }
    });

    $("body").confirmation({
            container:"body",
            btnOkClass:"btn btn-sm btn-success",
            btnCancelClass:"btn btn-sm btn-danger",
            selector: 'a[data-action="delete"]',
            onConfirm:function(event, element) {
                event.preventDefault()
                var id = element.attr('data-id');
                var image = element.attr('data-image');
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("admin.practiceDeleteImage") }}',
                    data: {id : id , image:image},
                    success: function(response)
                    {   
                        if (response.status == true) {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            }
        }); 
</script>
@endsection