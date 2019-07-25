@section('title','Ironbridge1779 | Practice Games')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
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
                                <p>{{ $practice_game->game->name }} <small class="form-text text-muted">must of [1024,2048,4096]</small></p>
                            @elseif($practice_game->game_id == '5b0e304b51b2010ec820fb4e')
                                <p>{{ $practice_game->game->name }} <small class="form-text text-muted">must of [12,35,70,140]</small></p>
                            @else
                                <p>{{ $practice_game->game->name }}</p>
                            @endif
                        </div>
                        <div class="col-md-3 practice_game_target" id="practice_game_target{{ $practice_game->id }}">
                            @if($practice_game->game_id == '5b0e304b51b2010ec820fb4e')
                                <p class="target_text">{{ $practice_game->variation_size }}</p>
                            @else
                                <p class="target_text">{{ $practice_game->target }}</p>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <a href="javascript:void(0)" class="practice_edit" id="practice_edit{{$practice_game->id}}" data-target="{{ $practice_game->target }}" data-game_id="{{ $practice_game->game_id }}" data-id="{{ $practice_game->id }}">
                                <i class="fa fa-pencil iconsetaddbox"></i>
                            </a>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
        
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
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
    </script>
@endsection