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
                <h3>Edit Event</h3>
            </div>
            <div class="col-md-6 text-right modalbuttonadd">
                <a href="{{ route('admin.event.index') }}" class="btn btn-info btn-md">Back</a>
            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <form method="POST" id="editEventForm" enctype="multipart/form-data">
            @csrf
            <!-- START BASIC DETAILS -->
            <div class="daingaemtitlebox">
                <h4>Basic Details</h4>
            </div>
            <div class="allbasicdirmain">                
                <div class="allbasicdirbox">                
                   
                    <div class="fdaboxallset">
                        <div class="form-group col-md-4">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Enter the name" value="@if(isset($event->name)){{$event->name}}@endif">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Type <small>(Single / Multi day(s))</small></label>
                            <select name="type" class="form-control">
                                <option>Select type</option>
                                <option value="single" @if(isset($event->type) && $event->type=='single') {{ 'selected' }} @endif>Single</option>
                                <option value="multi" @if(isset($event->type) && $event->type=='multi') {{ 'selected' }} @endif>Multi</option>
                            </select>
                        </div>
                        <input type="hidden" name="event_id" value="@if(isset($event->id)){{ $event->id }} @endif">
                        <div class="form-group col-md-4">
                            <label class="form-label">Coin Type</label>
                            <select name="coin_type" class="form-control">
                                <option>Select coin type</option>
                                <option value="ar" @if(isset($event->coin_type) && $event->coin_type=='ar') {{ 'selected' }} @endif>AR</option>
                                <option value="physical" @if(isset($event->coin_type) && $event->coin_type=='physical') {{ 'selected' }} @endif>PHYSICAL</option>
                            </select>
                        </div>
                    </div>
                    <div class="fdaboxallset">
                        <div class="form-group col-md-4">
                            <label class="form-label">City</label>
                            <select name="city_id" id="city_id" class="form-control">
                                <option>Select city</option>
                                @forelse($cities as $key=>$city)
                                <option value="{{ $city->_id }}" @if(isset($event->city_id) && $event->city_id==$city->_id) {{ 'selected' }} @endif>{{ $city->name }}</option>
                                @empty
                                <option>Record Not found</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="text" name="event_start_date" class="form-control" placeholder="Enter the start date" value="@if(isset($event->starts_at)){{ $event->starts_at->format('d-m-Y h:i A') }} @endif"  data-date-format="DD-MM-YYYY hh:mm A" id="startdate_basic" autocomplete="off">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="text" name="event_end_date" class="form-control" placeholder="Enter the date" value="@if(isset($event->ends_at)){{ $event->ends_at->format('d-m-Y h:i A') }}@endif"  data-date-format="DD-MM-YYYY hh:mm A" id="enddate_basic" autocomplete="off">
                        </div>
                    </div>

                    <div class="fdaboxallset">
                        <div class="form-group col-md-4">
                            <label class="form-label">Rejection Ratio<small> In percentage (enter 0 if no rejection ratio)</small></label>
                            <input type="text" name="rejection_ratio" class="form-control" placeholder="Enter the rejection ratio" value="@if(isset($event->rejection_ratio)){{ $event->rejection_ratio }} @endif">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Winning Ratio<small> In fix amount (enter 0 if no winning ratio)</small></label>
                            <input type="text" name="winning_ratio" class="form-control" id="winning_ratio" placeholder="Enter the winning ratio" value="@if(isset($event->winning_ratio)){{ $event->winning_ratio }}@endif">
                        </div>
                    </div>

                    <div class="fdaboxallset">
                        <div class="form-group col-md-4">
                            <label class="form-label">Fees & Gold</label>
                            <input type="text" name="fees" class="form-control" placeholder="Enter the fees" value="@if(isset($event->fees)){{ $event->fees }}@endif">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Discount date</label>
                            <input type="text" name="discount_date" class="form-control" id="discount_date" placeholder="Enter the discount date" value="@if(isset($event->discount_till)){{ $event->discount_till->format('d-m-Y') }}@endif" autocomplete="off">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Discount Fees (%)</label>
                            <input type="text" name="discount_fees" class="form-control" placeholder="Enter the discount fees" value="@if(isset($event->discount)){{ $event->discount }}@endif">
                        </div>
                    </div>


                    <div class="fdaboxallset">
                        <div class="form-group col-md-4">
                            <label class="form-label">Attempts</label>
                            <input type="text" name="attempts" class="form-control" placeholder="Enter the attempts" value="@if(isset($event->attempts)){{ $event->attempts }}@endif">
                        </div>
                        @if(isset($event->coin_type))
                            <div class="form-group col-md-4 coin_number_box @if(isset($event->coin_type) && $event->coin_type == 'ar'){{ 'hidden' }}@endif">
                                <label class="form-label">Coin Number</label>
                                <input type="text" name="coin_number" class="form-control" placeholder="Enter the coin number" value="@if(isset($event->coin_number)){{ $event->coin_number }}@endif">
                            </div>
                        @else
                            <div class="form-group col-md-4 coin_number_box hidden">
                                <label class="form-label">Coin Number</label>
                                <input type="text" name="coin_number" class="form-control" placeholder="Enter the coin number" value="@if(isset($event->coin_number)){{ $event->coin_number }}@endif">
                            </div>
                        @endif
                        <div class="form-group col-md-4">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" placeholder="Enter the description">@if(isset($event->description)){{ $event->description }}@endif</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END BASIC DETAILS -->

            <!-- GAME DETAILS START CODE -->
            <div class="col-md-12">
                <h4>Mini Game Details</h4>
            </div>
            <div class="separate_mini_game_box">
                <input type="hidden" name="event_id" value="{{ $event->_id }}">
                @if($event->mini_games)   
                    @forelse($event->mini_games as $key => $day)
                        <div class="mini_game">
                            <div class="daingaemtitlebox">
                                @if($event->type == 'single')
                                    <h5>Single Day</h5>
                                @else
                                    <h5>Day {{ $key+1 }}</h5>
                                @endif
                            </div>
                            @if($event->type == 'single')
                                <div class="form-group col-md-4">
                                    <label class="form-label">Start Date</label>
                                    <input type="text" class="form-control" placeholder="Enter the start date" value="{{ $event->starts_at->format('d-m-Y h:i A') }}" autocomplete="off" disabled="true">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">End Date</label>
                                    <input type="text" class="form-control" placeholder="Enter the end date" value="{{ $event->ends_at->format('d-m-Y h:i A') }}" autocomplete="off" disabled="true">
                                </div>
                                <input type="hidden" name="end_date[{{ $key }}]" value="{{ $event->ends_at->format('d-m-Y h:i A') }}">
                                <input type="hidden" name="start_date[{{ $key }}]" value="{{ $event->ends_at->format('d-m-Y h:i A') }}">
                            @else
                                <div class="form-group col-md-4">
                                    <label class="form-label">Start Date</label>
                                    <input type="text" name="start_date[{{ $key }}]" class="form-control datetimepicker" placeholder="Enter the start date" id="startdate0" value="{{ $day['from']->toDateTime()->format('d-m-Y h:i A') }}" autocomplete="off">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">End Date</label>
                                    <input type="text" name="end_date[{{ $key }}]" class="form-control datetimepicker" placeholder="Enter the end date" id="enddate0" value="{{ $day['to']->toDateTime()->format('d-m-Y h:i A') }}" autocomplete="off">
                                </div>
                            @endif
                            <div class="form-group col-md-4 day_section">
                                <br>
                                <!-- <a href="javascript:void(0)" class="btn btn-info add_game">Add Mini Game</a> -->
                                @if($event->type == 'multi')
                                    <!-- <a href="javascript:void(0)" class="btn add_mini_game">Add Days</a> -->
                                    <?php
                                        $eventCount = count($event->mini_games)-1;
                                    ?>
                                    @if($eventCount == $key)
                                        @if($eventCount == 1)
                                            <a href="javascript:void(0)" class="btn remove_mini_game">Remove Day</a>
                                        @endif
                                        <a href="javascript:void(0)" class="btn add_mini_game">Add Days</a>
                                    @else
                                        <a href="javascript:void(0)" class="btn remove_mini_game">Remove Day</a>
                                    @endif
                                @endif



                            </div>
                            <input type="hidden" name="last_mini_game_index" value="{{ $key }}">
                            
                            <div class="separate_game_box">
                                @forelse($day['games'] as $index => $miniGame)
                                    <div class="game_box">
                                        <div class="daingaemtitlebox">
                                            <h6>Mini Game</h6>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label class="form-label">Game</label>
                                            <select name="game_id[{{ $key }}][{{$index}}]" class="form-control games">
                                                <option value="">Select Game</option>
                                                @forelse($games as $game)
                                                <option value="{{ $game['_id'] }}" @if($miniGame['game_info']['id']==$game['_id']){{ 'selected' }} @endif data-identifier="{{ $game['identifier'] }}">{{ $game['name'] }}</option>
                                                @empty
                                                <option>Record Not found</option>
                                                @endforelse
                                            </select>
                                        </div>
                                        <input type="hidden" name="last_elem_index" value="{{ $index }}">

                                        <div class="variation_box">
                                            <?php
                                                $miniGameId = $miniGame['game_info']['id'];
                                            ?>
                                            <?php if($miniGameId == '5b0e2ff151b2010ec820fb48'){ ?>
                                                <!-- Sudoku -->
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Variation size</label>
                                                    <input type="text"  name="variation_size[{{$key}}][{{$index}}]" value="{{ $miniGame['variation_data']['variation_size'] }}"  class="form-control">
                                                </div>
                                               
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Sudoku Id</label>
                                                    <select name="sudoku_id[{{$key}}][{{$index}}]" class="form-control">
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 1) { echo "selected";} ?> value="1">1</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 2) { echo "selected";} ?> value="2">2</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 3) { echo "selected";} ?> value="3">3</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 4) { echo "selected";} ?> value="4">4</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 5) { echo "selected";} ?> value="5">5</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 6) { echo "selected";} ?> value="6">6</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 7) { echo "selected";} ?> value="7">7</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 8) { echo "selected";} ?> value="8">8</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 9) { echo "selected";} ?> value="9">9</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 10) { echo "selected";} ?> value="10">10</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 11) { echo "selected";} ?> value="11">11</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 12) { echo "selected";} ?> value="12">12</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 13) { echo "selected";} ?> value="13">13</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 14) { echo "selected";} ?> value="14">14</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 15) { echo "selected";} ?> value="15">15</option>
                                                        <option <?php if ($miniGame['variation_data']['sudoku_id'] == 16) { echo "selected";} ?> value="16">16</option>
                                                    </select>
                                                </div>
                                            <?php } else if($miniGameId == '5b0e303f51b2010ec820fb4d'){ ?>
                                                <!-- Number search -->
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Row</label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['row'] }}" name="row[{{$key}}][{{$index}}]" id="row" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Column</label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['column'] }}" name="column[{{$key}}][{{$index}}]" id="column" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Number Generate</label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['number_generate'] }}" name="number_generate[{{$key}}][{{$index}}]" id="number_generate" class="form-control">
                                                </div>
                                            <?php } else if($miniGameId == '5b0e304b51b2010ec820fb4e'){ ?>
                                                <!-- Jigsaw Puzzle -->
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Variation size <small class="form-text text-muted">must of [12,35,70,140]</small></label>
                                                    <input type="text"  name="variation_size[{{$key}}][{{$index}}]" value="{{ $miniGame['variation_data']['variation_size'] }}"  class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Variation Image <small class="form-text text-muted">must be 2000*1440 dimension</small></label>
                                                    <input type="file"  name="variation_image[{{$key}}][{{$index}}]" class="form-control" multiple>
                                                    @if(isset($miniGame['variation_data']['variation_image']))
                                                        <input type="hidden" name="hide_image[{{$key}}][{{$index}}]" value="{{ $miniGame['variation_data']['variation_image'] }}">
                                                        <br/>
                                                        <img src="{{ asset('storage/events/'.$miniGame['variation_data']['variation_image']) }}" width="100px">
                                                    @endif
                                                    <br/>
                                                </div>

                                            <?php } else if($miniGameId == '5b0e306951b2010ec820fb4f'){ ?>
                                                <!-- Sliding Puzzle -->
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Variation size</label>
                                                    <input type="text"  name="variation_size[{{$key}}][{{$index}}]" value="{{ $miniGame['variation_data']['variation_size'] }}"  class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Variation Image <small class="form-text text-muted">must be 1024*1024 dimension</small></label>
                                                    <input type="file"  name="variation_image[{{$key}}][{{$index}}]" class="form-control">
                                                    
                                                    @if(isset($miniGame['variation_data']['variation_image']))
                                                        <input type="hidden" name="hide_image[{{$key}}][{{$index}}]" value="{{ $miniGame['variation_data']['variation_image'] }}">
                                                        <br/>
                                                        <img src="{{ asset('storage/events/'.$miniGame['variation_data']['variation_image']) }}" width="100px">
                                                    @endif
                                                </div>

                                            <?php } else if($miniGameId == '5bfba3afc3169d169062a3b3'){ ?>
                                                <!-- Word Search -->
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Row</label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['row'] }}" name="row[{{$key}}][{{$index}}]" id="row" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Column</label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['column'] }}" name="column[{{$key}}][{{$index}}]" id="column" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Target</label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['target'] }}" name="target[{{$key}}][{{$index}}]" id="target" class="form-control">
                                                </div>
                                            <?php } else if($miniGameId == '5c188ab5719a1408746c473b'){ ?>
                                                <!-- 2048 -->
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Row <small class="form-text text-muted">must of [4,6,8]</small></label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['row'] }}" name="row[{{$key}}][{{$index}}]" id="row" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Column <small class="form-text text-muted">must of [4,6,8]</small></label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['column'] }}" name="column[{{$key}}][{{$index}}]" id="column" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Target <small class="form-text text-muted">must of [512,1024,2048,4096]</small></label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['target'] }}" name="target[{{$key}}][{{$index}}]" id="target" class="form-control">
                                                </div>
                                            <?php } else if($miniGameId == '5c188b06719a1408746c473c'){ ?>
                                                <!-- Block Game -->
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Row <small class="form-text text-muted">must of [9,10]</small></label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['row'] }}" name="row[{{$key}}][{{$index}}]" id="row" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Column <small class="form-text text-muted">must of [9,10]</small></label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['column'] }}" name="column[{{$key}}][{{$index}}]" id="column" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Target</label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['target'] }}" name="target[{{$key}}][{{$index}}]" id="target" class="form-control">
                                                </div>
                                            
                                            <?php } else if($miniGameId == '5c39a1f3697b251760c0d5fc'){ ?>
                                                <!-- Bubble Shooter -->
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Target</label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['target'] }}" name="target[{{$key}}][{{$index}}]" id="target" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">No Of balls</label>
                                                    <input type="text"  value="{{ $miniGame['variation_data']['no_of_balls'] }}" name="no_of_balls[{{$key}}][{{$index}}]" id="no_of_balls" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Bubble level id</label>
                                                    <input type="text"  value="{{ $miniGame['variation_data']['bubble_level_id'] }}" name="bubble_level_id[{{$key}}][{{$index}}]" id="bubble_level_id" class="form-control">
                                                </div>
                                            
                                            <?php } else if($miniGameId == '5c80fd106650bf31a808abed' || $miniGameId == '5c80fd226650bf31a808abee' || $miniGameId == '5c5d282b697b25205433531d' || $miniGameId == '5c5d279c697b25205433531c' || $miniGameId == '5c399831697b251760c0d5e2'){ ?>
                                                <!-- Slices -->
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Target</label>
                                                    <input type="text" value="{{ $miniGame['variation_data']['target'] }}" name="target[{{$key}}][{{$index}}]" id="target" class="form-control">
                                                </div>
                                            <?php } ?>
                                         
                                        
                                        </div>
                                        
                                        <div class="col-md-8 button_section">
                                            <br>
                                            <?php
                                                $miniGameCount = count($day['games'])-1;
                                            ?>
                                            @if($miniGameCount == $index)
                                                <a href="javascript:void(0)" class="btn remove_game remove_game1">Remove Mini Game</a>
                                                <a href="javascript:void(0)" class="btn add_game">Add Mini Game</a>
                                            @else
                                                <a href="javascript:void(0)" class="btn remove_game">Remove Mini Game</a>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="game_box">
                                        <div class="daingaemtitlebox">
                                            <h6>Mini Game</h6>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="form-label">Game</label>
                                            <select name="game_id[0][]" class="form-control games">
                                                <option value="">Select Game</option>
                                                @forelse($games as $key=>$game)
                                                <option value="{{ $game['_id'] }}">{{ $game['name'] }}</option>
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
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                    @endforelse
                @else
                    <div class="mini_game">
                        <div class="daingaemtitlebox">
                            @if($event->type == 'single')
                                <h5>Single Day</h5>
                            @else
                                <h5>Day 1</h5>
                            @endif
                        </div>
                        @if($event->type == 'single')
                            <div class="form-group col-md-4">
                                <label class="form-label">Start Date</label>
                                <input type="text" class="form-control" placeholder="Enter the start date" value="{{ $event->starts_at->format('d-m-Y h:i A') }}" autocomplete="off" disabled="true">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">End Date</label>
                                <input type="text" class="form-control" placeholder="Enter the end date" value="{{ $event->ends_at->format('d-m-Y h:i A') }}" autocomplete="off" disabled="true">
                            </div>
                            <input type="hidden" name="end_date[0]" value="{{ $event->ends_at->format('d-m-Y h:i A') }}">
                            <input type="hidden" name="start_date[0]" value="{{ $event->ends_at->format('d-m-Y h:i A') }}">
                        @else
                            <div class="form-group col-md-4">
                                <label class="form-label">Start Date</label>
                                <input type="text" name="start_date[0]" class="form-control datetimepicker" placeholder="Enter the start date" id="startdate0" value="" autocomplete="off">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">End Date</label>
                                <input type="text" name="end_date[0]" class="form-control datetimepicker" placeholder="Enter the end date" id="enddate0" value="" autocomplete="off">
                            </div>
                        @endif
                        <div class="form-group col-md-4">
                            <br>
                            <!-- <a href="javascript:void(0)" class="btn btn-info add_game">Add Mini Game</a> -->
                            @if($event->type =='multi')
                                <a href="javascript:void(0)" class="btn add_mini_game">Add Days</a>
                            @endif
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
                                        <option value="">Select Game</option>
                                        @forelse($games as $key=>$game)
                                        <option value="{{ $game['_id'] }}" data-identifier="{{ $game['identifier'] }}">{{ $game['name'] }}</option>
                                        @empty
                                        <option>Record Not found</option>
                                        @endforelse
                                    </select>
                                </div>
                                
                                <input type="hidden" name="last_elem_index" value="0">
                                <div class="variation_box"></div>
                                <div class="col-md-8 button_section">
                                    <br>
                                    <a href="javascript:void(0)" class="btn add_game">Add Mini Game</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
            </div>
            <!-- END GAME DETAILS -->


            <!-- HUNT CLUE START CODE -->
            <div class="daingaemtitlebox">
                <h4>Hunt Details</h4>
            </div>
            <div class="allbasicdirmain">                
                <div class="allbasicdirbox">
                    <input type="hidden" name="event_id" value="{{ $id }}"> 
                    <div class="form-group col-md-4">
                        <label class="form-label">Map Reveal Date</label>
                        <input type="text" name="map_reveal_date" class="form-control" id="map_reveal_date" placeholder="Enter the map reveal date" autocomplete="off" value="@if(isset($event->map_reveal_date)){{$event->map_reveal_date->format('d-m-Y h:i A') }}@endif">
                    </div>
                    <div class="form-group col-md-5">
                        <label class="form-label">Search Place Name</label>
                       
                        <select class="form-control hunts" name="search_place_name" id="hunts">
                            <option value="">Select Place</option>
                            @forelse($hunts as $hunt)
                                <option value="{{ $hunt->id }}" @if($event->hunt_id == $hunt->id){{ 'selected' }} @endif>{{ $hunt->name }}</option>
                            @empty
                            @endforelse
                        </select>
                        <!-- <input type="text" name="search_place_name" id="search_place_name" class="form-control" placeholder="Enter the Search Place Name" autocomplete="off"> -->
                    </div>
                    <div class="form-group col-md-3">
                        <div class="addhunteyrefbtn">
                            <a href="{{ route('admin.add_location') }}" target='_blank' class="btn" >Add Hunt</a>
                            <a href="{{ route('admin.boundary_map',$event->hunt_id) }}" target='_blank' class="btn hunt_details"><i class="fa fa-eye "></i></a>
                            <a href="javascript:void(0)" class="btn" id="refresh"><i class="fa fa-refresh"></i></a>
                        </div>
                    </div>
                </div>
                
            </div>
            <!-- END HUNT CLUE  -->

            <!-- PRIZE START CODE -->
            <div class="daingaemtitlebox">
                <h4>Prizes</h4>
            </div>
            <div class="allbasicdirmain">                
                <div class="allbasicdirbox">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label class="form-label">Group Type</label>
                        </div>
                        
                        <div class="form-group col-md-3">
                            <label>Rank</label>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Prize</label>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Prize type</label>
                        </div>
                    </div>
                    <div id="prize_box">
                        @forelse($event->prizes as $key => $value)
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <select class="form-control group_type" name="group_type[{{ $key }}]">
                                        <option value="individual" @if(isset($value->group_type) && $value->group_type == "individual") {{ 'selected' }} @endif>Individual</option>
                                        <option value="group" @if(isset($value->group_type) && $value->group_type == "group") {{ 'selected' }} @endif>Group</option>
                                    </select>
                                </div>
                                <input type="hidden" name="prize_index" value="{{ $key }}">
                                <div class="form-group col-md-3 rank_box">
                                    @if(isset($value->group_type) && $value->group_type == "individual")
                                        <input type="text" name="rank[{{ $key }}]" class="form-control" placeholder="Rank" value="{{ $value->rank }}">
                                    @else
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" name="start_rank[{{ $key }}]" class="form-control col-md-12" placeholder="Start" value="{{ $value->start_rank }}">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="end_rank[{{ $key }}]" class="form-control col-md-12" placeholder="End" value="{{ $value->end_rank }}">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="text" name="prize[{{ $key }}]" class="form-control" placeholder="Prize" value="{{ $value->prize_value }}">
                                </div>
                                <div class="form-group col-md-2">
                                    <select class="form-control" name="prize_type[{{ $key }}]">
                                        <option value="cash" @if(isset($value->prize_type) && $value->prize_type == "cash") {{ 'selected' }} @endif>Cash</option>
                                        <option value="gold" @if(isset($value->prize_type) && $value->prize_type == "gold") {{ 'selected' }} @endif>Gold</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2 button_box">
                                    <?php
                                        $totalPrize = count($event->prizes)-1;
                                    ?>
                                    @if($totalPrize == $key)
                                        <a href="javascript:void(0)" class="btn add_prize"><i class="fa fa-plus "></i></a>
                                        <a href="javascript:void(0)" class="btn remove_prize"><i class="fa fa-minus "></i></a>
                                    @else
                                        <a href="javascript:void(0)" class="btn remove_prize"><i class="fa fa-minus "></i></a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <select class="form-control group_type" name="group_type[]">
                                        <option value="individual">Individual</option>
                                        <option value="group">Group</option>
                                    </select>
                                </div>
                                <input type="hidden" name="prize_index" value="0">
                                <div class="form-group col-md-3 rank_box">
                                    <input type="text" name="rank[]" class="form-control" placeholder="Rank">
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="text" name="prize[]" class="form-control" placeholder="Prize">
                                </div>
                                <div class="form-group col-md-2">
                                    <select class="form-control" name="prize_type[]">
                                        <option value="cash">Cash</option>
                                        <option value="gold">Gold</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2 button_box">
                                    <a href="javascript:void(0)" class="btn add_prize"><i class="fa fa-plus "></i></a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="form-group Submitnextbtn">
                    <button type="submit" class="btn btn-success btnSubmit">SUBMIT</button>
                </div>
            </div>
            <!-- PRIZE START CODE -->      

            
    </form>
    </div>
</div>


@endsection

@section('scripts')
    
    <script type="text/javascript">
        /* SUBMIT FORM */
        $(document).on('submit','#editEventForm',function(e){
            e.preventDefault();
            formData = new FormData($(this)[0]);

            $.ajax({
                type:'POST',
                url:'{{ route("admin.event.updateEvent","") }}',
                data: formData,
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: false,
                processData: false,
                beforeSend:function(){},
                success:function(response) {
                    if (response.status == true) {
                        toastr.success(response.message);
                    } else {
                        toastr.warning(response.message);
                    }
                },
                complete:function(){},
                error:function(){}
            });
        })        
        

        
    </script>
     <script type="text/javascript">
        $(function () {
            $('#city_id').select2();

            $('#discount_date').datepicker({
                startDate: new Date()
            });
            
            $('#startdate').datetimepicker({
                useCurrent: false,
                format: "DD-MM-YYYY hh:mm A",
                minDate: moment()
            });

            $('#enddate').datetimepicker({
                useCurrent: false,
                format: "DD-MM-YYYY hh:mm A",
                minDate: moment()
            });
            

            $('#startdate').datetimepicker().on('dp.change', function (e) {
                var incrementDay = moment(new Date(e.date));
                if ($('select[name="type"]').val() == 'single') {
                    incrementDay.add(0, 'days');
                     $('#enddate').data('DateTimePicker').setMinDate(incrementDay);
                } else {
                    incrementDay.add(1, 'days');
                    $('#enddate').data('DateTimePicker').setMinDate(incrementDay);
                }
                $(this).data("DateTimePicker").hide();
            });

            $('#enddate').datetimepicker().on('dp.change', function (e) {
                var decrementDay = moment(new Date(e.date));
                decrementDay.subtract(1, 'days');
                $('#startdate').data('DateTimePicker').setMaxDate(decrementDay);
                $(this).data("DateTimePicker").hide();
            });

            
            $(document).on('change','select[name="coin_type"]', function () {
                    $('.coin_number_box').addClass('hidden');
                if($(this).val() == 'physical'){
                    $('.coin_number_box').removeClass('hidden')
                }
            });

        });
    </script>


    <!-- MINI GAMES -->
    <script type="text/javascript">
        /* DATE TIME PICKER */
        //$('.datetimepicker').datetimepicker();
        var startdate = '{{ $event->starts_at }}';
        var enddate = '{{ $event->ends_at }}';
        // var enddate = '{{ $event->ends_at }}';
        
         
        
        $('#startdate0').datetimepicker({
            useCurrent: false,
            format: "DD-MM-YYYY hh:mm A",
            minDate: moment(startdate),
            maxDate: moment(enddate),
        });
        $('#enddate0').datetimepicker({
            useCurrent: false,
            format: "DD-MM-YYYY hh:mm A",
            minDate: moment(startdate),
            maxDate: moment(enddate),
        });

        $('#startdate0').datetimepicker().on('dp.change', function (e) {
            var incrementDay = moment(new Date(e.date));
            incrementDay.add(1, 'days');
            $('#enddate0').data('DateTimePicker').setMinDate(incrementDay);
            $(this).data("DateTimePicker").hide();
        });

        $('#enddate0').datetimepicker().on('dp.change', function (e) {
            var decrementDay = moment(new Date(e.date));
            decrementDay.subtract(1, 'days');
            $('#startdate0').data('DateTimePicker').setMaxDate(decrementDay);
            $(this).data("DateTimePicker").hide();
        });

        $(document).ready(function() {
            /* APPEND GAME */
            $(document).on('click','.add_game',function(){
                let gameIndexMaintainer = $(this).parents('.game_box').find('input[name=last_elem_index]');
                let miniGameIndexMaintainer = $(this).parents('.mini_game').find('input[name=last_mini_game_index]');

                let lastIndex = gameIndexMaintainer.val();
                let gameIndex = parseInt(lastIndex)+1;
                $('.remove_game1').remove();
                //gameIndexMaintainer.val(gameIndex);

                let currentIndex = miniGameIndexMaintainer.val();

                let defaultMGHtml = `<div class="game_box">
                                <div class="daingaemtitlebox">
                                   <h6>Mini Game</h6>
                                </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Game</label>
                                <select name="game_id[`+currentIndex+`][`+gameIndex+`]" class="form-control games">
                                    <option value="">Select Game</option>
                                    @forelse($games as $key=>$game)
                                    <option value="{{ $game['_id'] }}" data-identifier="{{ $game['identifier'] }}">{{ $game['name'] }}</option>
                                    @empty
                                    <option>Record Not found</option>
                                    @endforelse
                                </select>
                            </div>
                            <input type="hidden" name="last_elem_index" value="`+gameIndex+`">
                            <div class="variation_box"></div>
                            
                            <div class="col-md-8 button_section">
                                <br>
                                <a href="javascript:void(0)" class="btn remove_game1">Remove Mini Game</a>
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
                                            <input type="text" name="start_date[`+currentIndex+`]" class="form-control datetimepicker" placeholder="Enter the start date" id="startdate`+currentIndex+`" autocomplete="off">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="form-label">End Date</label>
                                            <input type="text" name="end_date[`+currentIndex+`]" class="form-control datetimepicker" placeholder="Enter the end date" id="enddate`+currentIndex+`" autocomplete="off">
                                        </div>
                                        <div class="form-group col-md-4 day_section">
                                            <br>
                                            <!-- <a href="javascript:void(0)" class="btn add_game">Add Mini Game</a> -->
                                            <a href="javascript:void(0)" class="btn remove_mini_game">Remove Day</a>
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
                                                        <option value="">Select Game</option>
                                                        @forelse($games as $key=>$game)
                                                        <option value="{{ $game['_id'] }}" data-identifier="{{ $game['identifier'] }}">{{ $game['name'] }}</option>
                                                        @empty
                                                        <option>Record Not found</option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                                
                                                <input type="hidden" name="last_elem_index" value="0">
                                                <div class="variation_box"></div>
                                                
                                                <div class="col-md-8 button_section">
                                                    <br>
                                                    <a href="javascript:void(0)" class="btn remove_game1">Remove Mini Game</a>
                                                    <a href="javascript:void(0)" class="btn add_game">Add Mini Game</a>
                                                    <!-- <a href="javascript:void(0)" class="btn add_mini_game">Add Days</a> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;

                $(this).parents('.mini_game').after(defaultMGHtml);
                $(this).parents('.col-md-4').html('<br/><a href="javascript:void(0)" class="btn remove_mini_game">Remove Day</a>');
                $(this).parents('.col-md-4').find('.add_mini_game').remove();

                
                //$('.datetimepicker').datetimepicker();


                $('#startdate'+currentIndex).datetimepicker({
                    useCurrent: false,
                    format: "DD-MM-YYYY hh:mm A",
                    minDate: moment(startdate),
                    maxDate: moment(enddate),
                    defaultDate: moment(startdate),
                });
                $('#enddate'+currentIndex).datetimepicker({
                    useCurrent: false,
                    format: "DD-MM-YYYY hh:mm A",
                    minDate: moment(startdate),
                    maxDate: moment(enddate),
                    defaultDate: moment(startdate),
                });
                $('#startdate'+currentIndex).datetimepicker().on('dp.change', function (e) {
                    var incrementDay = moment(new Date(e.date));
                    incrementDay.add(1, 'days');
                    $('#enddate'+currentIndex).data('DateTimePicker').setMinDate(incrementDay);
                    $(this).data("DateTimePicker").hide();
                });

                $('#enddate'+currentIndex).datetimepicker().on('dp.change', function (e) {
                    var decrementDay = moment(new Date(e.date));
                    decrementDay.subtract(1, 'days');
                    $('#startdate'+currentIndex).data('DateTimePicker').setMaxDate(decrementDay);
                     $(this).data("DateTimePicker").hide();
                });


            });

            /* REMOVE Mini GAME */
            $(document).on('click','.remove_game , .remove_game1',function(){
                $(this).parents('.game_box').remove();
                $('.game_box:last').find('.add_game').remove();
                $('.game_box:last').find('.button_section').append(' <a href="javascript:void(0)" class="btn add_game">Add Mini Game</a>');
            });

            /* REMOVE Day */
            $(document).on('click','.remove_mini_game',function(){
                $(this).parents('.mini_game').remove();
                if($('.mini_game').length == 1){
                    $('.remove_mini_game:last , .add_mini_game:last').remove();
                }
                $('.mini_game:last').find('.day_section').append(' <a href="javascript:void(0)" class="btn add_mini_game">Add Days</a>');

                for (var i = 0; i < $('.mini_game').length; i++) {
                    $('.mini_game::nth-child('+(i+1)+')').find('h5').text('Day '+(i+1));
                }

            });

            /* IMAGE APPEND IN JIGSAW AND SLIDING PUZZLE */
            $(document).on('change','.games',function(){
                let game = $(this).val();
                let currentIndex = $(this).parents('.mini_game').find('input[name=last_mini_game_index]').val();
                let gameIndex = $(this).parents('.game_box').find('input[name=last_elem_index]').val();
                var identifier = $(this).find(':selected').data('identifier');

                $(this).parents('.game_box').find('.variation_image_box').remove();
                
                if (identifier == 'sudoku'){
                    var data = `@include("admin.event.add_event.add_model_variations.add_sudoku",['index' => 
                    ['game_index'=>'`+gameIndex+`','current_index'=>'`+currentIndex+`']])`;
                }else if(identifier == 'number_search'){
                    var data = `@include("admin.event.add_event.add_model_variations.add_numberSearch",['index' => 
                    ['game_index'=>'`+gameIndex+`','current_index'=>'`+currentIndex+`']])`;
                }else if(identifier == 'jigsaw'){
                    var data = `@include("admin.event.add_event.add_model_variations.add_jigsaw",['index' => 
                    ['game_index'=>'`+gameIndex+`','current_index'=>'`+currentIndex+`']])`;
                }else if(identifier == 'sliding'){
                    var data = `@include("admin.event.add_event.add_model_variations.add_sliding",['index' => 
                    ['game_index'=>'`+gameIndex+`','current_index'=>'`+currentIndex+`']])`;
                }else if(identifier == '2048'){
                    var data = `@include("admin.event.add_event.add_model_variations.add_2048",['index' => 
                    ['game_index'=>'`+gameIndex+`','current_index'=>'`+currentIndex+`']])`;
                }else if(identifier == 'block'){
                    var data = `@include("admin.event.add_event.add_model_variations.add_blockGame",['index' => 
                    ['game_index'=>'`+gameIndex+`','current_index'=>'`+currentIndex+`']])`;
                }else if(identifier == 'word_search'){
                    var data = `@include("admin.event.add_event.add_model_variations.add_wordSearch",['index' => 
                    ['game_index'=>'`+gameIndex+`','current_index'=>'`+currentIndex+`']])`;
                }else if(identifier == 'hexa'){
                    var data = `@include("admin.event.add_event.add_model_variations.add_hexa",['index' => 
                    ['game_index'=>'`+gameIndex+`','current_index'=>'`+currentIndex+`']])`;
                }else if(identifier == 'bubble_shooter'){
                    var data = `@include("admin.event.add_event.add_model_variations.add_bubble_shooter",['index' => 
                    ['game_index'=>'`+gameIndex+`','current_index'=>'`+currentIndex+`']])`;
                }else if(identifier == 'slices'){
                    var data = `@include("admin.event.add_event.add_model_variations.add_slices",['index' => 
                    ['game_index'=>'`+gameIndex+`','current_index'=>'`+currentIndex+`']])`;
                }else if(identifier == 'yatzy'){
                    var data = `@include("admin.event.add_event.add_model_variations.add_yatzy",['index' => 
                    ['game_index'=>'`+gameIndex+`','current_index'=>'`+currentIndex+`']])`;
                }else if(identifier == 'snake'){
                    var data = `@include("admin.event.add_event.add_model_variations.add_snake",['index' => 
                    ['game_index'=>'`+gameIndex+`','current_index'=>'`+currentIndex+`']])`;
                }else if(identifier == 'domino'){
                    var data = `@include("admin.event.add_event.add_model_variations.add_domino",['index' => 
                    ['game_index'=>'`+gameIndex+`','current_index'=>'`+currentIndex+`']])`;
                } else {
                    data = '<input type="hidden" name="sudoku_id" value="0" /><input type="hidden" name="row" value="0" /><input type="hidden" name="column" value="0" /><input type="hidden" name="number_generate" value="0">';
                }

                $(this).parents('.game_box').find('.variation_box').html(data);

                


            });

        });
    </script>
    <!-- END MINI GAME -->


    <!-- HUNT AND EVENT MODULE -->
    <script type="text/javascript">
        /* DATE TIME PICKER */
        $('#map_reveal_date').datetimepicker({
            useCurrent: false,
            format: "DD-MM-YYYY hh:mm A",
            minDate: moment(startdate),
            maxDate: moment(enddate),
            defaultDate: moment(startdate),
        });

        /* SUBMIT FORM */
        $(document).ready(function() {
     
            $(document).on('submit','#addEventForm',function(e){
                e.preventDefault();
                formData = new FormData($(this)[0]);
                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.event.addHuntDetails") }}',
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){},
                    success:function(response) {
                        if (response.status == true) {
                            toastr.success(response.message);
                            window.location.href = '{{ route("admin.event.index")}}';
                        } else {
                            toastr.warning(response.message);
                        }
                    },
                    complete:function(){},
                    error:function(){}
                });
            })

            $('.hunts').select2();

            $(document).on('change','select[name="search_place_name"]',function(){
                var hunt_id = $(this).val();
                var url = "{{ route('admin.boundary_map','/') }}/"+hunt_id;
                $('.hunt_details').attr('href',url).attr('target','_blank');
            });

            $(document).on('click','.hunt_details',function(){
                var url = $(this).attr('href');
                if (url == 'javascript:void(0)') {
                    toastr.warning('Please select the search place name');   
                }
            });

            /* REFRESH BUTTON */
            $(document).on('click','#refresh',function(){
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("admin.event.getHuntList") }}',
                    data: {id : '{{ $id }}'},
                    success: function(response)
                    {

                        if (response.status == true) {
                            $('select[name="search_place_name"]').html('');
                            jQuery.each( response.data, function( index, val ) {
                                
                                $('select[name="search_place_name"]').append('<option value="'+val._id+'">'+val.name+'</option>')
                            });
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            });


            /* APPEND PRIZES */
            $(document).on('click','.add_prize',function(){
                $(this).parents('.button_box').find('.remove_prize').remove();
                $(this).parents('.button_box').append('<a href="javascript:void(0)" class="btn remove_prize"><i class="fa fa-minus "></i></a>');
                $(this).remove();
                let prizeIndex = $('input[name="prize_index"]:last').val();
                let currentIndex = parseInt(prizeIndex)+1;

                $('#prize_box').append(`<div class="row">
                            <div class="form-group col-md-3">
                                <select class="form-control group_type" name="group_type[`+currentIndex+`]">
                                    <option value="individual">Individual</option>
                                    <option value="group">Group</option>
                                </select>
                            </div>
                            <input type="hidden" name="prize_index" value="`+currentIndex+`">
                            <div class="form-group col-md-3 rank_box">
                                <input type="text" name="rank[`+currentIndex+`]" class="form-control" placeholder="Rank">
                            </div>
                            <div class="form-group col-md-2">
                                <input type="text" name="prize[`+currentIndex+`]" class="form-control" placeholder="Prize">
                            </div>
                            <div class="form-group col-md-2">
                                <select class="form-control" name="prize_type[`+currentIndex+`]">
                                    <option value="cash">Cash</option>
                                    <option value="gold">Gold</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2 button_box">
                                <a href="javascript:void(0)" class="btn add_prize"><i class="fa fa-plus "></i></a>
                                <a href="javascript:void(0)" class="btn remove_prize"><i class="fa fa-minus "></i></a>
                            </div>
                        </div>`);
            });


            $(document).on('click','.remove_prize',function(){
                $(this).parents('.row').remove();
                    $('.add_prize').remove();
                    $('#prize_box .row:last').find('.button_box').prepend(`<a href="javascript:void(0)" class="btn add_prize"><i class="fa fa-plus "></i></a>`);
                    if($('#prize_box .row').length == 1){
                        $('.remove_prize').remove();
                    }
            });

            $(document).on('change','.group_type',function(){
                var group_type = $(this).val();
                let prizeIndex = $(this).parents('.row').find('input[name="prize_index"]').val();
                if (group_type == 'group') {
                    $(this).parents('.row').find('.rank_box').html(`<div class="row">
                                                                    <div class="col-md-6">
                                                                        <input type="text" name="start_rank[`+prizeIndex+`]" class="form-control col-md-12" placeholder="Start">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" name="end_rank[`+prizeIndex+`]" class="form-control col-md-12" placeholder="End">
                                                                    </div>
                                                                </div>`);
                } else if(group_type == 'individual'){

                    $(this).parents('.row').find('.rank_box').html(`<input type="text" name="rank[`+prizeIndex+`]" class="form-control" placeholder="Rank">`);
                }
            });

        });
    </script>
    <!-- HUNT -->
@endsection