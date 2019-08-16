@section('title','Ironbridge1779 | GAME VARIATION')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
@endsection
@section('content')
<style type="text/css">
    #map {
        height: 400px;
        width: 100%;
      }
</style>
<div class="right_paddingboxpart">      
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Event</h3>
            </div>
            <!-- <div class="col-md-6 text-right modalbuttonadd">
                <a href="{{ route('admin.event.index') }}" class="btn btn-info btn-md">Back</a>
            </div> -->
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <form method="POST" id="addEventForm" enctype="multipart/form-data">
            @csrf
            <div class="col-md-12">
                <h4>Mini Game Details</h4>
            </div>
            <input type="hidden" name="id" value="{{ $id }}">
            <div class="separate_mini_game_box">
                    
                <div class="mini_game">
                    <div class="daingaemtitlebox">
                        <h5>Day 1</h5>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="text" name="start_date[0]" class="form-control datetimepicker" placeholder="Enter the start date">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="text" name="end_date[0]" class="form-control datetimepicker" placeholder="Enter the end date">
                    </div>
                    <div class="form-group col-md-4">
                        <br>
                        <!-- <a href="javascript:void(0)" class="btn btn-info add_game">Add Mini Game</a> -->
                        <a href="javascript:void(0)" class="btn add_mini_game">Add Days</a>
                    </div>
                    <input type="hidden" name="last_mini_game_index" value="0">
                    
                    <div class="separate_game_box">
                        <div class="game_box">
                             <div class="daingaemtitlebox">
                                <h6>Mini Game</h6>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Game</label>
                                <select name="game_id[0][]" class="form-control games">
                                    <option>Select Game</option>
                                    @forelse($games as $key=>$game)
                                    <option value="{{ $game['_id'] }}" data-identifier="{{ $game['identifier'] }}">{{ $game['name'] }}</option>
                                    @empty
                                    <option>Record Not found</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Row</label>
                                <input type="text" name="row[0][]" class="form-control" placeholder="Enter the row">
                            </div>
                            <input type="hidden" name="last_elem_index" value="0">
                            <div class="form-group col-md-4">
                                <label class="form-label">Column</label>
                                <input type="text" name="column[0][]" class="form-control" placeholder="Enter the column">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Target</label>
                                <input type="text" name="target[0][]" class="form-control" placeholder="Enter the target">
                            </div>
                            <div class="col-md-4 button_section">
                                <br>
                                <a href="javascript:void(0)" class="btn add_game">Add Mini Game</a>
                                <!-- <a href="javascript:void(0)" class="btn btn-info add_mini_game">Add Days</a> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END GAME DETAILS -->
            <div class="form-group col-md-12">
                <a href="{{ route('admin.event.basicDetails',$id) }}" class="btn btn-success btnSubmit">PREVIOUS</a>
                <button type="submit" class="btn btn-success btnSubmit">SUBMIT & GO NEXT</button>
            </div>
    </form>
    </div>
</div>


@endsection

@section('scripts')
    <script type="text/javascript">
        /* DATE TIME PICKER */
        $('.datetimepicker').datetimepicker();
        
        $(document).ready(function() {
            /* APPEND GAME */
            $(document).on('click','.add_game',function(){
                let gameIndexMaintainer = $(this).parents('.game_box').find('input[name=last_elem_index]');
                let miniGameIndexMaintainer = $(this).parents('.mini_game').find('input[name=last_mini_game_index]');

                let lastIndex = gameIndexMaintainer.val();
                let gameIndex = parseInt(lastIndex)+1;
                console.log(lastIndex);
                //gameIndexMaintainer.val(gameIndex);

                let currentIndex = miniGameIndexMaintainer.val();

                let defaultMGHtml = `<div class="game_box">
                                <div class="daingaemtitlebox">
                                   <h6>Mini Game</h6>
                                </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Game</label>
                                <select name="game_id[`+currentIndex+`][`+gameIndex+`]" class="form-control games">
                                    <option>Select Game</option>
                                    @forelse($games as $key=>$game)
                                    <option value="{{ $game['_id'] }}" data-identifier="{{ $game['identifier'] }}">{{ $game['name'] }}</option>
                                    @empty
                                    <option>Record Not found</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Row</label>
                                <input type="text" name="row[`+currentIndex+`][`+gameIndex+`]" class="form-control" placeholder="Enter the row">
                            </div>
                            <input type="hidden" name="last_elem_index" value="`+gameIndex+`">
                            <div class="form-group col-md-4">
                                <label class="form-label">Column</label>
                                <input type="text" name="column[`+currentIndex+`][`+gameIndex+`]" class="form-control" placeholder="Enter the column">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Target</label>
                                <input type="text" name="target[`+currentIndex+`][`+gameIndex+`]" class="form-control" placeholder="Enter the target">
                            </div>
                            <div class="col-md-4 button_section">
                                <br>
                                <a href="javascript:void(0)" class="btn add_game">Add Mini Game</a>
                              </div>
                        </div>`;
                $(this).parents('.game_box').after(defaultMGHtml);
                $(this).parents('.button_section').append('<a href="javascript:void(0)" class="btn remove_game">Remove Mini Game</a>');
                $(this).parents('.button_section').find('.add_game').remove();
            });

            /* APPEND MINI GAME */
            $(document).on('click','.add_mini_game',function(){
                let miniGameIndexMaintainer = $(this).parents('.mini_game').find('input[name=last_mini_game_index]');
                let lastIndex = miniGameIndexMaintainer.val();
                let currentIndex = parseInt(lastIndex)+1;
                
                let defaultMGHtml = `<div class="mini_game">
                                        <div class="daingaemtitlebox">
                                            <h5>Day `+(currentIndex+1)+`</h5>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="form-label">Start Date</label>
                                            <input type="text" name="start_date[`+currentIndex+`]" class="form-control datetimepicker" placeholder="Enter the start date">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="form-label">End Date</label>
                                            <input type="text" name="end_date[`+currentIndex+`]" class="form-control datetimepicker" placeholder="Enter the end date">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <br>
                                            <!-- <a href="javascript:void(0)" class="btn add_game">Add Mini Game</a> -->
                                            <a href="javascript:void(0)" class="btn add_mini_game">Add Days</a>
                                        </div>
                                        <input type="hidden" name="last_mini_game_index" value="`+currentIndex+`">
                                        <div class="separate_game_box">
                                            <div class="game_box">
                                                <div class="daingaemtitlebox">
                                                   <h6>Mini Game</h6>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Game</label>
                                                    <select name="game_id[`+currentIndex+`][]" class="form-control games">
                                                        <option>Select Game</option>
                                                        @forelse($games as $key=>$game)
                                                        <option value="{{ $game['_id'] }}" data-identifier="{{ $game['identifier'] }}">{{ $game['name'] }}</option>
                                                        @empty
                                                        <option>Record Not found</option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Row</label>
                                                    <input type="text" name="row[`+currentIndex+`][]" class="form-control" placeholder="Enter the row">
                                                </div>
                                                <input type="hidden" name="last_elem_index" value="0">
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Column</label>
                                                    <input type="text" name="column[`+currentIndex+`][]" class="form-control" placeholder="Enter the column">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Target</label>
                                                    <input type="text" name="target[`+currentIndex+`][]" class="form-control" placeholder="Enter the target">
                                                </div>
                                                <div class="col-md-4 button_section">
                                                    <br>
                                                    <a href="javascript:void(0)" class="btn add_game">Add Mini Game</a>
                                                    <!-- <a href="javascript:void(0)" class="btn add_mini_game">Add Days</a> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;

                $(this).parents('.mini_game').after(defaultMGHtml);
                $(this).parents('.col-md-4').append('<a href="javascript:void(0)" class="btn remove_mini_game">Remove Day</a>');
                $(this).parents('.col-md-4').find('.add_mini_game').remove();
                
                $('.datetimepicker').datetimepicker();

            });

            /* REMOVE Mini GAME */
            $(document).on('click','.remove_game',function(){
                $(this).parents('.game_box').remove();
            });

            /* REMOVE Day */
            $(document).on('click','.remove_mini_game',function(){
                $(this).parents('.mini_game').remove();
            });

            /* IMAGE APPEND IN JIGSAW AND SLIDING PUZZLE */
            $(document).on('change','.games',function(){
                let game = $(this).val();
                let currentIndex = $(this).parents('.mini_game').find('input[name=last_mini_game_index]').val();
                let gameIndex = $(this).parents('.game_box').find('input[name=last_elem_index]').val();
                

                $(this).parents('.game_box').find('.variation_image_box').remove();
                if (game == '5b0e306951b2010ec820fb4f') {
                    //sliding
                    $(this).parents('.game_box').find('.form-group:last').after(`
                            <div class="form-group col-md-4 variation_image_box">
                                <label class="form-label">Variation Image <small class="form-text text-muted">must be 1024*1024 dimension</small></label>
                                <input type="file" name="variation_image[`+currentIndex+`][`+gameIndex+`]" class="form-control">
                            </div>`);   
                } else if(game == '5b0e304b51b2010ec820fb4e'){
                    //jigsaw
                    $(this).parents('.game_box').find('.form-group:last').after(`
                            <div class="form-group col-md-4 variation_image_box">
                                <label class="form-label">Variation Image <small class="form-text text-muted">must be 2000*1440 dimension</small></label>
                                <input type="file" name="variation_image[`+currentIndex+`][`+gameIndex+`]" class="form-control">
                            </div>`);
                    
                }
            });

        });
    </script>
    <script type="text/javascript">

        /* SUBMIT FORM */
        $(document).on('submit','#addEventForm',function(e){
            e.preventDefault();
            formData = new FormData($(this)[0]);
            $.ajax({
                type:'POST',
                url:'{{ route("admin.event.addMiniGame") }}',
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                beforeSend:function(){},
                success:function(response) {
                    if (response.status == true) {
                        toastr.success(response.message);
                        window.location.href = '{{ route("admin.event.huntDetails","/")}}/'+response.id;
                    } else {
                        toastr.warning(response.message);
                    }
                },
                complete:function(){},
                error:function(){}
            });
        })        
    </script>
@endsection