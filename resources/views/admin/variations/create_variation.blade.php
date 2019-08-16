@section('title','Ironbridge1779 | GAME VARIATION')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
<div class="right_paddingboxpart">      
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Add Game Variation</h3>
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
            <div class="form-group col-md-6">
                <label class="form-label">Game Name</label>
                <select name="game_id" id="game_id" class="form-control">
                    <option>Select Game</option>
                    @forelse($games as $key=>$game)
                    <option value="{{ $game['_id'] }}" data-identifier="{{ $game['identifier'] }}">{{ $game['name'] }}</option>
                    @empty
                    <option>Record Not found</option>
                    @endforelse
                </select>
            </div>
            <input type="hidden" name="identifier" value="">
            <div class="form-group col-md-6">
                <label class="form-label">Variation Name</label>
                <input type="text" name="variation_name" id="variation_name" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label class="form-label">Variation Complexity</label>
                <select name="variationComplexity" class="form-control">
                    <option value="">Select One</option>
                    <option value="complex">Complex</option>
                    <option value="normal">Normal</option>
                    <option value="easy">Easy</option>
                </select>
            </div>
            <div id="game_wise_data"></div>
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
            //limit image
            $(document).on("change",'#variation_image',function() {
                    var input = document.getElementById('variation_image');
                    if(input.files.length == 3){
                        $(".btnSubmit").attr("disabled", false);
                    } else {
                        $('.error1').remove();
                        $('#variation_image').after('<label class="error error1">Only 3 image upload</label>');
                        $(".btnSubmit").attr("disabled", true);
                    }
            });

            /*$(document).on("change",'input[name="row"]',function() {
                var select_game = $('select[name="game_id"]').find(':selected').data('identifier');
                console.log(select_game);
            });*/


            $(document).on('change','select[name="game_id"]',function(){
                var identifier = $(this).find(':selected').data('identifier');
                $('input[name="identifier"]').val(identifier);
                if (identifier == 'sudoku'){
                    var data = `@include("admin.variations.partial_variations.add_model_variations.add_sudoku")`;
                }else if(identifier == 'number_search'){
                    var data = `@include("admin.variations.partial_variations.add_model_variations.add_numberSearch")`;
                }else if(identifier == 'jigsaw'){
                    var data = `@include("admin.variations.partial_variations.add_model_variations.add_jigsaw")`;
                }else if(identifier == 'sliding'){
                    var data = `@include("admin.variations.partial_variations.add_model_variations.add_sliding")`;
                }else if(identifier == '2048'){
                    var data = `@include("admin.variations.partial_variations.add_model_variations.add_2048")`;
                }else if(identifier == 'block'){
                    var data = `@include("admin.variations.partial_variations.add_model_variations.add_blockGame")`;
                }else if(identifier == 'word_search'){
                    var data = `@include("admin.variations.partial_variations.add_model_variations.add_wordSearch")`;
                }else if(identifier == 'hexa'){
                    var data = `@include("admin.variations.partial_variations.add_model_variations.add_hexa")`;
                }else if(identifier == 'bubble_shooter'){
                    var data = `@include("admin.variations.partial_variations.add_model_variations.add_bubble_shooter")`;
                }else if(identifier == 'slices'){
                    var data = `@include("admin.variations.partial_variations.add_model_variations.add_slices")`;
                }else if(identifier == 'yatzy'){
                    var data = `@include("admin.variations.partial_variations.add_model_variations.add_yatzy")`;
                }else if(identifier == 'snake'){
                    var data = `@include("admin.variations.partial_variations.add_model_variations.add_snake")`;
                }else if(identifier == 'domino'){
                    var data = `@include("admin.variations.partial_variations.add_model_variations.add_domino")`;
                } else {
                    data = '<input type="hidden" name="sudoku_id" value="0" /><input type="hidden" name="row" value="0" /><input type="hidden" name="column" value="0" /><input type="hidden" name="number_generate" value="0">';
                }
                $("#game_wise_data").html(data);
            })


            


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
                            if(gameIdfentifier == '2048' || gameIdfentifier == 'word_search' || gameIdfentifier == 'block' || gameIdfentifier == 'hexa' || gameIdfentifier == 'slices' || gameIdfentifier == 'yatzy'|| gameIdfentifier == 'snake'|| gameIdfentifier == 'domino' || gameIdfentifier == 'bubble_shooter'){
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
                    "variation_image[]":{
                        accept: "image/jpg,image/jpeg,image/png,image/gif",
                        required:function(){
                            let gameIdfentifier = $('#game_id').find(':selected').attr('data-identifier');
                            if(gameIdfentifier == 'sliding' || gameIdfentifier == 'jigsaw'){
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
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
                    console.log(formData);
                    formData.append('gameName', $('#game_id option:selected').text());
                    $.ajax({
                        type:'POST',
                        url:'{{ route("admin.gameVariation.store") }}',
                        data: formData,
                        cache:false,
                        contentType: false,
                        processData: false,
                        beforeSend:function(){},
                        success:function(response) {
                            if (response.status == true) {
                                toastr.success(response.message);
                                window.location.href = '{{ route("admin.gameVariation.index")}}';
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