@section('title','Ironbridge1779 | NEWS')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">      
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Edit Game Variation</h3>
            </div>
            <div class="col-md-6 text-right modalbuttonadd">
                <a href="{{ route('admin.gameVariation.index') }}" class="btn btn-info btn-md">Back</a>
            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <form method="POST" id="form_validation" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group col-md-6">
                <label class="form-label">Game Name</label>
                <select disabled name="game_id" id="game_id" class="form-control">
                    <option>Select Game</option>
                    @forelse($games as $key=>$game)
                        <option value="{{ $game['_id'] }}" data-identifier="{{ $game['identifier'] }}" <?php if($game['_id']==$variations->game_id){ echo 'selected'; } ?>>{{ $game['name'] }}</option>
                    @empty
                    <option>Record Not found</option>
                    @endforelse
                </select>
            </div>
            <input type="hidden" name="identifier" value="{{ $variations->game->identifier }}">
            <div class="form-group col-md-6">
                <label class="form-label">Variation Name</label>
                <input type="text" name="variation_name" value="{{ $variations->variation_name }}" id="variation_name" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label class="form-label">Variation Complexity</label>
                <select name="variationComplexity" class="form-control">
                    <option value="">Select One</option>
                    <option value="complex" {{ ($variations->variation_complexity=='complex')?'selected':'' }}>Complex</option>
                    <option value="normal" {{ ($variations->variation_complexity=='normal')?'selected':'' }}>Normal</option>
                    <option value="easy" {{ ($variations->variation_complexity=='easy')?'selected':'' }}>Easy</option>
                </select>
            </div>
            <input type="hidden" name="game_variations_id" id="game_variations_id" value="{{ $variations->id }}">
            <?php if($variations->game->identifier == 'sudoku'){ ?>
                @include("admin.variations.partial_variations.edit_model_variations.edit_sudoku")
            <?php } else if($variations->game->identifier == 'number_search'){ ?>
                @include("admin.variations.partial_variations.edit_model_variations.edit_numberSearch")
            <?php } else if($variations->game->identifier == 'jigsaw'){ ?>
                @include("admin.variations.partial_variations.edit_model_variations.edit_jigsaw")
            <?php } else if($variations->game->identifier == 'sliding'){ ?>
                @include("admin.variations.partial_variations.edit_model_variations.edit_sliding")
            <?php } else if($variations->game->identifier == 'word_search'){ ?>
                @include("admin.variations.partial_variations.edit_model_variations.edit_wordSearch")
            <?php } else if($variations->game->identifier == '2048'){ ?>
                @include("admin.variations.partial_variations.edit_model_variations.edit_2048")
            <?php } else if($variations->game->identifier == 'block'){ ?>
                @include("admin.variations.partial_variations.edit_model_variations.edit_blockGame")
            <?php } else if($variations->game->identifier == 'hexa'){ ?>
                @include("admin.variations.partial_variations.edit_model_variations.edit_hexa")
            <?php } else if($variations->game->identifier == 'bubble_shooter'){ ?>
                @include("admin.variations.partial_variations.edit_model_variations.edit_bubble_shooter")
            <?php } else if($variations->game->identifier == 'snake'){ ?>
                @include("admin.variations.partial_variations.edit_model_variations.edit_snake")
            <?php } else if($variations->game->identifier == 'domino'){ ?>
                @include("admin.variations.partial_variations.edit_model_variations.edit_domino")
            <?php } else if($variations->game->identifier == 'slices'){ ?>
                @include("admin.variations.partial_variations.edit_model_variations.edit_slices")
            <?php } else if($variations->game->identifier == 'yatzy'){ ?>
                @include("admin.variations.partial_variations.edit_model_variations.edit_yatzy")
            <?php } else{ ?>
                <input type="hidden" name="sudoku_id" value="0">
                <input type="hidden" name="row" value="0">
                <input type="hidden" name="column" value="0">
                <input type="hidden" name="number_generate" value="0">
            <?php } ?>
            <input type="hidden" id="total_image" value="{{ count($variations->variation_image) }}">
            <div id="photo_section" class="imageslibbox">
                <ul>
                    @if(isset($variations->variation_image) && $variations->variation_image!="")
                        @forelse($variations->variation_image as $key => $image)
                            <!-- <a data-fancybox="gallery" href="{{ $image }}">
                                <img width="100" src="{{ $image }}">
                            </a> -->
                            <?php 
                                $image1 = substr(strrchr($image,'/'),1);
                            ?>
                            <li>
                                <div class="closeicon">
                                    <a target="_blank" href="javascript:void(0)" data-action="delete" data-id="{{ $variations->id }}" data-index="{{ $key }}" >
                                        <img src="{{ asset('admin_assets/svg/close.svg') }}">
                                    </a>
                                </div>
                                <div class="photosboxset">
                                    <img width="100" src="{{ $image }}">
                                </div>
                                <input type="hidden" name="old_variation_image[]" value="{{ $image1 }}">
                            </li>
                        @empty
                        @endforelse
                    @endif
                </ul>
            </div>
             
            <div class="form-group col-md-12">
                <button type="submit" class="btn btn-success btnSubmit">Submit</button>
            </div>
    </form>
    </div>
</div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            //DELETE IMAGE
            $(document).on("click",'a[data-action="delete"]',function() {
                var id = $(this).data('id');
                var index = $(this).data('index');
                $(this).parents('li').remove();
                var total_image = $('#total_image').val();
                var image_len = $('.closeicon').length;
                $('#total_image').val(image_len);
                var total_image = $('#total_image').val();
                
                if(total_image >= 3){
                    $(".btnSubmit").attr("disabled", false);
                    // $('#variation_image').attr("disabled", true);
                } else {
                    total_image = 3-total_image;
                    $('.error1').remove();
                    $('#variation_image').after('<label class="error error1">Only '+total_image+' image upload</label>');
                    $(".btnSubmit").attr("disabled", true);
                    // $('#variation_image').attr("disabled", false);
                }
                /*$.ajax({
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("admin.deleteImage") }}',
                    data: {id : id , index:index},
                    success: function(response)
                    {
                        if (response.status == true) {
                            toastr.success(response.message);
                            table.ajax.reload();
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });*/
            });



            $(document).on("change",'#variation_image',function() {
                    var input = document.getElementById('variation_image');
                    var total_image = parseInt($('#total_image').val());
                    //total_image = 3-total_image;
                    var total_file = input.files.length;
                                        
                    if((total_file+total_image) == 3){
                        $('#total_image').val('3');
                        $('.error1').remove();
                        $(".btnSubmit").attr("disabled", false);
                        // $('#variation_image').attr("disabled", true);
                    } else {
                        // $('#variation_image').attr("disabled", false);
                        total_image = 3-total_image;
                        $('.error1').remove();
                        $('#variation_image').after('<label class="error error1">Only '+total_image+' image upload</label>');
                        $(".btnSubmit").attr("disabled", true);
                    }
            });

            $(document).on("click",'#reset_file',function() {
                $('.error1').remove();
                $(".btnSubmit").attr("disabled", false);
                $('#variation_image').val('');
            });

            //ADD VARIATION
            $('#form_validation').submit(function(e) {
                    e.preventDefault();
                })
            .validate({
            //$('#form_validation').validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    game_list_id: {
                        required: true
                    },
                    variation_name: {
                        required: true
                    },
                    variationComplexity:{
                        required:true
                    },
                    variationSize:{
                        digits:true,
                        required:function(){
                            let gameIdfentifier = $('#game_id').find(':selected').attr('data-identifier');
                            if(gameIdfentifier == 'sudoku' || gameIdfentifier == 'jigsaw' || gameIdfentifier == 'sliding'){
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                    sudoku_id:{
                        required:function(){
                            let gameIdfentifier = $('#game_id').find(':selected').attr('data-identifier');
                            if(gameIdfentifier == 'sudoku'){
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                    target:{
                        digits:true,
                        required:function(){
                            let gameIdfentifier = $('#game_id').find(':selected').attr('data-identifier');
                            if(gameIdfentifier == '2048' || gameIdfentifier == 'word_search' || gameIdfentifier == 'block' || gameIdfentifier == 'hexa' || gameIdfentifier == 'slices' || gameIdfentifier == 'yatzy'|| gameIdfentifier == 'snake'|| gameIdfentifier == 'domino'|| gameIdfentifier == 'bubble_shooter'){
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                    row:{
                        digits:true,
                        required:function(){
                            let gameIdfentifier = $('#game_id').find(':selected').attr('data-identifier');
                            if(gameIdfentifier == '2048' || gameIdfentifier == 'word_search' || gameIdfentifier == 'block' || gameIdfentifier == 'number_search'){
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                    column:{
                        digits:true,
                        required:function(){
                            let gameIdfentifier = $('#game_id').find(':selected').attr('data-identifier');
                            if(gameIdfentifier == '2048' || gameIdfentifier == 'word_search' || gameIdfentifier == 'block' || gameIdfentifier == 'number_search'){
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                    number_generate:{
                        digits:true,
                        required:function(){
                            let gameIdfentifier = $('#game_id').find(':selected').attr('data-identifier');
                            if(gameIdfentifier == 'number_search'){
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                    /*"variation_image[]":{
                        extension:"jpg|png|jpeg|gif",
                        required:function(){
                            let gameIdfentifier = $('#game_id').find(':selected').attr('data-identifier');
                            if(gameIdfentifier == 'sliding' || gameIdfentifier == 'jigsaw'){
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },*/
                    no_of_balls:{
                        digits:true,
                        required:function(){
                            let gameIdfentifier = $('#game_id').find(':selected').attr('data-identifier');
                            if(gameIdfentifier == 'bubble_shooter'){
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                    bubble_level_id:{
                        digits:true,

                        required:function(){
                            let gameIdfentifier = $('#game_id').find(':selected').attr('data-identifier');
                            if(gameIdfentifier == 'bubble_shooter'){
                                return true;
                            } else {
                                return false;
                            }
                        },
                        min : function(){
                            var gameIdfentifier = $('#game_id').find(':selected').attr('data-identifier');
                            if (gameIdfentifier=='bubble_shooter') {
                                if($('input[name="bubble_level_id"]').val()>0){
                                    return true;
                                } else {
                                    return 1;
                                }
                            } else {
                                return false;
                            }

                        },
                        max : function(){
                            var gameIdfentifier = $('#game_id').find(':selected').attr('data-identifier');
                            if (gameIdfentifier=='bubble_shooter') {
                                if($('input[name="bubble_level_id"]').val()>0 && $('input[name="bubble_level_id"]').val()<=5){
                                    //return true;
                                } else {
                                    return 5;
                                }
                            } else {
                                return false;
                            }
                        },
                    },

                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    formData.append('gameName', $('#game_id option:selected').text());
                    var id = $('#game_variations_id').val();
                    $.ajax({
                        type:'POST',
                        url:'{{ route("admin.gameVariation.update","/") }}/'+id,
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
                }
            });
        });
    </script>
@endsection