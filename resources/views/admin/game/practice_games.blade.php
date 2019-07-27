@section('title','Ironbridge1779 | Practice Games')
@extends('admin.layouts.admin-app')
@section('styles')
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="datingactivity_box">
            <h3>Practice Games Targets</h3>
            <div class="innerdatingactivity">
                @forelse($practiceGames as $practice_game)
                    <div class="swoped_detlisbox" id="practice_game{{ $practice_game->id }}">
                        <div class="col-md-7">
                            @if($practice_game->game_id == '5c188ab5719a1408746c473b')
                                <p>{{ $practice_game->game->name }} <small class="form-text text-muted">must of [512,1024,2048,4096]</small></p>
                            @elseif($practice_game->game_id == '5b0e304b51b2010ec820fb4e')
                                <p>{{ $practice_game->game->name }} <small class="form-text text-muted">must of [12,35,70,140]</small></p>
                            @else
                                <p>{{ $practice_game->game->name }}</p>
                            @endif
                        </div>
                        <div class="col-md-3 practice_game_target" id="practice_game_target{{ $practice_game->id }}">
                            @if($practice_game->game_id == '5b0e2ff151b2010ec820fb48')
                                <p class="target_text">{{ $practice_game->variation_size }}</p>
                                <?php $target = $practice_game->variation_size ?>
                            
                            @elseif($practice_game->game_id == '5b0e303f51b2010ec820fb4d')
                                <p class="target_text">{{ $practice_game->number_generate }}</p>
                                <?php $target = $practice_game->number_generate ?>
                            @else
                                <p class="target_text">{{ $practice_game->target }}</p>
                                <?php $target = $practice_game->target ?>
                            @endif
                        </div>

                        <div class="col-md-2">
                            <a href="javascript:void(0)" class="practice_edit" id="practice_edit{{$practice_game->id}}" data-target="{{ $target }}" data-game_id="{{ $practice_game->game_id }}" data-id="{{ $practice_game->id }}">
                                <i class="fa fa-pencil iconsetaddbox"></i>
                            </a>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
        <div class="datingactivity_box">
            <h3>More Games</h3>
            <div class="innerdatingactivity">
                <form id="formPracticeGames" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group col-md-6">
                        <label class="form-label">Game Name</label>
                        <select name="game_id" class="form-control game_id">
                            @forelse($moregame as $game)
                                <option value="{{ $game->game->id }}" data-id="{{ $game->id }}">{{ $game->game->name }}</option>
                            @empty
                            @endforelse  
                        </select>
                    </div>
                    <div class="variation_box">
                        
                    </div>
                    
                    <div class="form-group col-md-6">
                        <br/>
                        <input type="submit" name="submit" class="btn btn-success" value="Submit">
                    </div>
                </form>
                <div class="multi_variation_image imageslibbox1">
                    
                </div>
            </div>
        </div>
        
    </div>
@forelse($moregame as $game)
    <div id="variation{{$game->id}}" style="display: none;">
        <input type="hidden" name="practice_game" value="{{ $game->id }}">
        <div class="form-group col-md-6">
            <label class="form-label">Variation size <small class="hidden size_hint">must of [12,35,70,140]</small></label>
            <input type="text" name="variation_size" value="{{ $game->variation_size }}" class="form-control">
        </div>
        <div class="form-group col-md-6">
            <label class="form-label">Variation Image <small class="hidden image_hit">must be 2000*1440 dimension </small></label>
            <input type="file" name="variation_image[]" class="form-control variation_image">
        </div>
    </div>
    <div id="photo_section{{$game->id}}" class="imageslibbox1" style="display: none;">
        <h3>{{ $game->game->name }}</h3>
        <ul>
        @forelse($game->variation_image as $variation_image)
            <li>
                <div class="closeicon">
                    <a target="_blank" href="javascript:void(0)" data-action="delete" data-id="{{ $game->id }}"  data-image="{{ $variation_image }}">
                        <img src="{{ asset('admin_assets/svg/close.svg') }}">
                    </a>
                </div>
                <div class="photosboxset">
                    <img width="100" src="{{ $variation_image }}">
                </div>
            </li>
        @empty
            <li>
                <div class="photosboxset">
                    <p>No image found</p>
                </div>
            </li>
        @endforelse 
        </ul>
    </div>
@empty
@endforelse

@endsection

@section('scripts')
    <script type="text/javascript">
        $(window).load(function() {
            var id = $('.game_id').find(':selected').data('id');
            $('.variation_box').html($('#variation'+id).html());
            $('.multi_variation_image').html($('#photo_section'+id).html());

        });

        $(document).on('click','.practice_edit',function(){
            var id = $(this).data('id');
            var target = $(this).data('target');
            var game_id = $(this).data('game_id');
            $('.target , .practice_save , .practice_close').remove();
            $('.practice_edit , .target_text').removeClass('hidden');
            
            $(this).addClass('hidden').after('<a href="javascript:void(0)" class="practice_close" data-id="'+id+'">\
                        <i class="fa fa-times iconsetaddbox"></i>\
                    </a><a href="javascript:void(0)" class="practice_save" data-id="'+id+'" data-game_id="'+game_id+'">\
                        <i class="fa fa-save iconsetaddbox"></i>\
                    </a>');
            $('#practice_game_target'+id).find('p').addClass('hidden').after('<input type="number" name="target" id="target'+id+'" class="target" value="'+target+'">');
        });

        $(document).on('click','.practice_save',function(){
            var id = $(this).data('id');
            var game_id = $(this).data('game_id');
            var target = $('#target'+id).val();
            
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route("admin.gameTargetUpdate") }}',
                data: {id : id,target:target,game_id:game_id},
                beforeSend: function() {
                    $('.practice_save').find('i').addClass('fa-spinner');
                    $('.practice_close').remove();
                },
                success: function(response)
                {
                    if (response.status == true) {
                        $('.target , .practice_save , .practice_close').remove();
                        $('.practice_edit , .target_text').removeClass('hidden');
                        $('#practice_game_target'+id).find('p').html(target)
                        toastr.success(response.message);      
                    } else {
                        toastr.warning(response.message);
                    }
                }
            });
        })

        /* close button */
        $(document).on('click','.practice_close',function(){
            $('.practice_edit , .target_text').removeClass('hidden');
            $('.target , .practice_save , .practice_close').remove();
        });

        /* game select */
        $(document).on('change','.game_id',function(){
            var game_id = $(this).val();
            var id = $(this).find(':selected').data('id');
            
            if (game_id == '5b0e304b51b2010ec820fb4e') {
                $('.variation_box').html($('#variation'+id).html());
                $('.variation_image').attr('multiple',true);
                $('.image_hit , .size_hint').removeClass('hidden');
                $('.multi_variation_image').html($('#photo_section'+id).html());
            } else {
                $('.variation_box').html($('#variation'+id).html());
                $('.image_hit , .size_hint').addClass('hidden');
                $('.variation_image').attr('multiple',false);
                $('.multi_variation_image').html($('#photo_section'+id).html());
            }
        });

        /* submit form */
        $('#formPracticeGames').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url:'{{ route("admin.variationSizeUpdate") }}',
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                beforeSend:function(){},
                success:function(response) {
                    if (response.status == true) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.warning(response.message);
                    }
                },
                complete:function(){},
                error:function(){}
            });
        })

        /* delete image */
        $(document).on("click",'a[data-action="delete"]',function() {
            var id = $(this).data('id');
            var image = $(this).data('image');
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route("admin.practiceDeleteImage") }}',
                data: {id : id , image:image},
                success: function(response)
                {   
                    console.log(response);
                    if (response.status == true) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.warning(response.message);
                    }
                }
            });
        });
    </script>
@endsection