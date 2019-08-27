@section('title','Ironbridge1779 | Events')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
    
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
                <h3>Add Event</h3>
            </div>
            <div class="col-md-6 text-right modalbuttonadd">
                <a href="{{ route('admin.event.basicDetails',$id) }}" class="btn btn-info btn-md">Back</a>

            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <form method="POST" id="addEventForm" enctype="multipart/form-data">
            @csrf
            <!-- GAME DETAILS START CODE -->
            <div class="col-md-12">
                <div class="eventpagetitle">
                    <h4>Mini Game Details</h4>
                </div>
            </div>
            <div class="separate_mini_game_box">
                <input type="hidden" name="event_id" value="{{ $event->_id }}">
                @if($event->event_days)   
                    @forelse($event->event_days as $key => $day)
                        <div class="mini_game">
                            <div class="daingaemtitlebox boxmapedset">
                                @if($event->type == 'single')
                                    <h5>Single Day</h5>
                                @else
                                    <h5>Day {{ $key+1 }}</h5>
                                @endif
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-label">Date</label>
                            @if($event->type == 'single')
                                <input type="text" class="form-control datepicker" placeholder="Enter the Time"  value="{{ $day['from']->toDateTime()->format('d-m-Y') }}" autocomplete="off" disabled="true">
                                <input type="hidden" name="date[{{ $key }}]" class="form-control" value="{{ $event->ends_at->format('d-m-Y') }}">
                            @else
                                <input type="text" name="date[{{ $key }}]" class="form-control datepicker" placeholder="Enter the Time" value="{{ $day['from']->toDateTime()->format('d-m-Y') }}" autocomplete="off">
                            @endif
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-label">Start Time
                                    <a data-toggle="tooltip" title="Portal open time for that day" data-placement="right">?</a>
                                </label>
                                <input type="text" name="start_time[{{ $key }}]" class="form-control timepicker" placeholder="Enter the start Time" value="{{ $day['from']->toDateTime()->format('h:i A') }}" autocomplete="off">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-label">End Time
                                    <a data-toggle="tooltip" title="Portal close time for that day" data-placement="right">?</a>
                                </label>
                                <input type="text" name="end_time[{{ $key }}]" class="form-control timepicker" placeholder="Enter the end Time" value="{{ $day['to']->toDateTime()->format('h:i A') }}" autocomplete="off">
                            </div>
                            <div class="form-group col-md-3 day_section">
                                
                                <!-- <a href="javascript:void(0)" class="btn btn-info add_game">Add Mini Game</a> -->
                                @if($event->type == 'multi')
                                    <!-- <a href="javascript:void(0)" class="btn add_mini_game">Add Day</a> -->
                                    <?php
                                        $eventCount = count($event->event_days)-1;
                                    ?>
                                    @if($eventCount == $key)
                                        @if($eventCount != 0)
                                            <a href="javascript:void(0)" class="btn remove_mini_game">Remove Day</a>
                                        @endif
                                        <a href="javascript:void(0)" class="btn add_mini_game">Add Day</a>
                                    @else
                                        <a href="javascript:void(0)" class="btn remove_mini_game">Remove Day</a>
                                    @endif
                                @endif



                            </div>
                            <input type="hidden" name="last_mini_game_index" value="{{ $key }}">
                            
                            <div class="separate_game_box">
                                @forelse($day['mini_games'] as $index => $miniGame)
                                    <div class="game_box">
                                        <div class="form-group col-md-4">
                                            <label class="form-label">Game name</label>
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
                                                    <label class="form-label">Reveal Numbers <small class="form-text text-muted">must be between [1 to 81]</small></label>
                                                    <input type="text" name="variation_size[{{$key}}][{{$index}}]" class="form-control" value="{{ $miniGame['variation_data']['variation_size'] }}">
                                                </div>
                                                <input type="hidden" name="sudoku_id[{{$index['current_index']}}][{{$index['game_index']}}]" value="{{ $miniGame['variation_data']['sudoku_id'] }}">
                                                
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
                                                    <label class="form-label">Variation size <small class="form-text text-muted">must of [4,5,6]</small></label>
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
                                                <!-- <div class="form-group col-md-4">
                                                    <label class="form-label">Target</label>
                                                    <input type="text" value="@if(isset($miniGame['variation_data']['target'])){{ $miniGame['variation_data']['target'] }}@endif" name="target[{{$key}}][{{$index}}]" id="target" class="form-control">
                                                </div> -->
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
                                                <!-- <div class="form-group col-md-4">
                                                    <label class="form-label">Target <small class="form-text text-muted">must of [512,1024,2048,4096]</small></label>
                                                    <input type="text" value="@if(isset($miniGame['variation_data']['target'])){{ $miniGame['variation_data']['target'] }}@endif" name="target[{{$key}}][{{$index}}]" id="target" class="form-control">
                                                </div> -->
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
                                                <!-- <div class="form-group col-md-4">
                                                    <label class="form-label">Target</label>
                                                    <input type="text" value="@if(isset($miniGame['variation_data']['target'])){{ $miniGame['variation_data']['target'] }}@endif" name="target[{{$key}}][{{$index}}]" id="target" class="form-control">
                                                </div> -->
                                            
                                            <?php } else if($miniGameId == '5c39a1f3697b251760c0d5fc'){ ?>
                                                <!-- Bubble Shooter -->
                                                <!-- <div class="form-group col-md-4">
                                                    <label class="form-label">Target</label>
                                                    <input type="text" value="@if(isset($miniGame['variation_data']['target'])){{ $miniGame['variation_data']['target'] }}@endif" name="target[{{$key}}][{{$index}}]" id="target" class="form-control">
                                                </div> -->
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">No Of balls</label>
                                                    <input type="text"  value="{{ $miniGame['variation_data']['no_of_balls'] }}" name="no_of_balls[{{$key}}][{{$index}}]" id="no_of_balls" class="form-control">
                                                </div>                                            
                                            <?php } else if($miniGameId == '5c80fd106650bf31a808abed' || $miniGameId == '5c80fd226650bf31a808abee' || $miniGameId == '5c5d282b697b25205433531d' || $miniGameId == '5c5d279c697b25205433531c' || $miniGameId == '5c399831697b251760c0d5e2'){ ?>
                                                <!-- Slices -->
                                                <!-- <div class="form-group col-md-4">
                                                    <label class="form-label">Target</label>
                                                    <input type="text" value="@if(isset($miniGame['variation_data']['target'])){{ $miniGame['variation_data']['target'] }}@endif" name="target[{{$key}}][{{$index}}]" id="target" class="form-control">
                                                </div> -->
                                            <?php } ?>
                                         
                                        
                                        </div>
                                        
                                        <div class="col-md-4 button_section">
                                            
                                            <?php
                                                $miniGameCount = count($day['mini_games'])-1;
                                            ?>
                                            @if($miniGameCount == $index)
                                                @if($miniGameCount != 0)
                                                    <a href="javascript:void(0)" class="btn remove_game"><i class="fa fa-minus" aria-hidden="true"></i> Remove</a>
                                                @endif
                                                <a href="javascript:void(0)" class="btn add_game"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                                            @else
                                                <a href="javascript:void(0)" class="btn remove_game"><i class="fa fa-minus" aria-hidden="true"></i> Remove</a>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="game_box">
                                        <div class="form-group col-md-4">
                                            <label class="form-label">Game name</label>
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
                                        <!-- <div class="form-group col-md-4">
                                            <label class="form-label">Target</label>
                                            <input type="text" name="target[0][]" class="form-control" placeholder="Enter the target">
                                        </div> -->
                                        <div class="col-md-4 button_section">
                                            
                                            <a href="javascript:void(0)" class="btn add_game"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                    @endforelse
                @else
                    <div class="mini_game">
                        <div class="daingaemtitlebox boxmapedset">
                            @if($event->type == 'single')
                                <h5>Single Day</h5>
                            @else
                                <h5>Day 1</h5>
                            @endif

                        </div>
                        @if($event->type == 'single')
                            <div class="form-group col-md-3">
                                <label class="form-label">Date</label>
                                <input type="text" class="form-control datetimepicker" placeholder="Enter the time" id="date0" value="{{ $event->ends_at->format('d-m-Y') }}" autocomplete="off" disabled="true">
                                <input type="hidden" name="date[0]" class="form-control datetimepicker" value="{{ $event->ends_at->format('d-m-Y') }}">
                            </div>
                        @else
                            <div class="form-group col-md-3">
                                <label class="form-label">Date</label>
                                <input type="text" name="date[0]" class="form-control datetimepicker" placeholder="Enter the time" id="date0" value="" autocomplete="off">
                            </div>
                        @endif
                        
                        <input type="hidden" name="end_date[0]" value="{{ $event->ends_at->format('d-m-Y h:i A') }}">
                        <input type="hidden" name="start_date[0]" value="{{ $event->ends_at->format('d-m-Y h:i A') }}">
                        <div class="form-group col-md-3">
                            <label class="form-label">Start Time
                                <a data-toggle="tooltip" title="Portal open time for that day" data-placement="right">?</a>
                            </label>
                            <input type="text" name="start_time[0]" class="form-control" placeholder="Enter the start Time" id="starttime0" value="10:00 AM" autocomplete="off">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label">End Time
                                <a data-toggle="tooltip" title="Portal close time for that day" data-placement="right">?</a>
                            </label>
                            <input type="text" name="end_time[0]" class="form-control" placeholder="Enter the end Time" id="endtime0" value="10:00 PM" autocomplete="off">
                        </div>
                        <div class="form-group col-md-3 day_section">
                           
                            <!-- <a href="javascript:void(0)" class="btn btn-info add_game">Add Mini Game</a> -->
                            @if($event->type =='multi')
                                <a href="javascript:void(0)" class="btn add_mini_game">Add Day</a>
                            @endif
                        </div>
                        <input type="hidden" name="last_mini_game_index" value="0">
                        
                        <div class="separate_game_box">
                            <div class="game_box">
                                <div class="form-group col-md-4">
                                    <label class="form-label">Game name</label>
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
                                <div class="col-md-4 button_section">
                                    
                                    <a href="javascript:void(0)" class="btn add_game"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="form-group Submitnextbtn">
                    <a href="{{ route('admin.event.basicDetails',$id) }}" class="btn btn-success btnSubmit">PREVIOUS</a>
                    <button type="submit" class="btn btn-success btnSubmit">SUBMIT & GO NEXT</button>
                </div>
            </div>
            <!-- END GAME DETAILS -->
            
    </form>
    </div>
</div>


@endsection

@section('scripts')
    <script type="text/javascript">
        /* DATE TIME PICKER */
        //$('.datetimepicker').datetimepicker();
        var startdate = '{{ $event->starts_at }}';
        var enddate = '{{ $event->ends_at }}';
        

        $('#date0').datepicker({
            weekStart: 1,
            startDate: new Date(startdate),
            endDate: new Date(enddate),
            autoclose: true,
        }).datepicker("setDate", new Date(startdate))

        $('#starttime0').timepicker({
            //defaultTime: 'current',
            minuteStep: 15,
            disableFocus: true,
            template: 'dropdown'
        });
        $('#endtime0').timepicker({
            //defaultTime: 'current',
            minuteStep: 15,
            disableFocus: true,
            template: 'dropdown'
        });

        $('.datepicker').datepicker({
            weekStart: 1,
            startDate: new Date(startdate),
            endDate: new Date(enddate),
            autoclose: true,
        });

        $('.timepicker').timepicker({
            //defaultTime: 'current',
            minuteStep: 15,
            disableFocus: true,
            template: 'dropdown'
        });

        
        $(document).ready(function() {
            /* APPEND GAME */
            $('[data-toggle="tooltip"]').tooltip();   

            $(document).on('click','.add_game',function(){
                let gameIndexMaintainer = $(this).parents('.game_box').find('input[name=last_elem_index]');
                let miniGameIndexMaintainer = $(this).parents('.mini_game').find('input[name=last_mini_game_index]');

                let lastIndex = gameIndexMaintainer.val();
                let gameIndex = parseInt(lastIndex)+1;
                // $('.remove_game').remove();
                //gameIndexMaintainer.val(gameIndex);

                let currentIndex = miniGameIndexMaintainer.val();

                let defaultMGHtml = `<div class="game_box">
                            <div class="form-group col-md-4">
                                <label class="form-label">Game name</label>
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
                            
                            <div class="col-md-4 button_section">
                                
                                <a href="javascript:void(0)" class="btn remove_game"><i class="fa fa-minus" aria-hidden="true"></i> Remove</a>
                                <a href="javascript:void(0)" class="btn add_game"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                              </div>
                        </div>`;
                $(this).parents('.game_box').after(defaultMGHtml);
                $(this).parents('.button_section').html(' <a href="javascript:void(0)" class="btn remove_game"><i class="fa fa-minus" aria-hidden="true"></i> Remove</a>');
                $(this).parents('.button_section').find('.add_game').remove();
            });

            /* APPEND MINI GAME */
            $(document).on('click','.add_mini_game',function(){
                let miniGameIndexMaintainer = $(this).parents('.mini_game').find('input[name=last_mini_game_index]');
                let lastIndex = miniGameIndexMaintainer.val();
                let currentIndex = parseInt(lastIndex)+1;
                let defaultMGHtml = `<div class="mini_game">
                                        <div class="daingaemtitlebox boxmapedset">
                                            <h5>Day `+(currentIndex+1)+`</h5>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Date</label>
                                            <input type="text" name="date[`+currentIndex+`]" class="form-control datetimepicker" placeholder="Enter the time" id="date`+currentIndex+`" value="" autocomplete="off">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Start Time
                                                <a data-toggle="tooltip" title="Portal open time for that day" data-placement="right">?</a>
                                            </label>
                                            <input type="text" name="start_time[`+currentIndex+`]" class="form-control" placeholder="Enter the start Time" id="starttime`+currentIndex+`" value="10:00 AM" autocomplete="off">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="form-label">End Time
                                                <a data-toggle="tooltip" title="Portal close time for that day" data-placement="right">?</a>
                                            </label>
                                            <input type="text" name="end_time[`+currentIndex+`]" class="form-control" placeholder="Enter the end Time" id="endtime`+currentIndex+`" value="10:00 PM" autocomplete="off">
                                        </div>
                                        <div class="form-group col-md-3 day_section">
                                           
                                            <!-- <a href="javascript:void(0)" class="btn add_game"><i class="fa fa-plus" aria-hidden="true"></i> Add</a> -->
                                            <a href="javascript:void(0)" class="btn remove_mini_game">Remove Day</a>
                                            <a href="javascript:void(0)" class="btn add_mini_game">Add Day</a>
                                        </div>
                                        <input type="hidden" name="last_mini_game_index" value="`+currentIndex+`">
                                        <div class="separate_game_box">
                                            <div class="game_box">
                                                <div class="form-group col-md-4">
                                                    <label class="form-label">Game name</label>
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
                                                
                                                <div class="col-md-4 button_section">
                                                    
                                                    <a href="javascript:void(0)" class="btn add_game"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                                                    <!-- <a href="javascript:void(0)" class="btn add_mini_game">Add Day</a> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;

                $(this).parents('.mini_game').after(defaultMGHtml);
                $(this).parents('.col-md-3').html('<a href="javascript:void(0)" class="btn remove_mini_game">Remove Day</a>');
                $(this).parents('.col-md-3').find('.add_mini_game').remove();

                
                //$('.datetimepicker').datetimepicker();
                var myDate = new Date(startdate);
                myDate.setDate(myDate.getDate() + currentIndex);
                
                $('#date'+currentIndex).datepicker({
                    weekStart: 1,
                    startDate: new Date(startdate),
                    endDate: new Date(enddate),
                    autoclose: true,
                }).datepicker("update", myDate); 

                $('#starttime'+currentIndex).timepicker({
                    //defaultTime: 'current',
                    minuteStep: 15,
                    disableFocus: true,
                    template: 'dropdown'
                });
                $('#endtime'+currentIndex).timepicker({
                    //defaultTime: 'current',
                    minuteStep: 15,
                    disableFocus: true,
                    template: 'dropdown'
                });

                $('[data-toggle="tooltip"]').tooltip();   

            });

            /* REMOVE Mini GAME */
            $(document).on('click','.remove_game',function(){
                //$(this).parents('.mini_game').find('.game_box:last').find('.button_section').html(' <a href="javascript:void(0)" class="btn remove_game">Remove Mini Game</a> <a href="javascript:void(0)" class="btn add_game">Add Mini Game</a>');
                var gameIndex = $(this).parents('.game_box').find('[name="last_elem_index"]').val(); 
                var currentIndex = $(this).parents('.mini_game').find('[name="last_mini_game_index"]').val(); 
                $(this).parents('.mini_game').attr('id','mini_game'+currentIndex);
                
                $(this).parents('.game_box').remove();
                $('#mini_game'+currentIndex).find('.game_box').find('.add_game').remove();
                $('#mini_game'+currentIndex).find('.game_box:last').find('.button_section').append(' <a href="javascript:void(0)" class="btn add_game"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>');
                if($('#mini_game'+currentIndex).find('.game_box').length == 1){
                    $('#mini_game'+currentIndex).find('.game_box').find('.remove_game').remove();
                }
            });

            /* REMOVE Day */
            $(document).on('click','.remove_mini_game',function(){
                $(this).parents('.mini_game').remove();
                $('.add_mini_game').remove();
                $('.mini_game:last').find('.day_section').append(' <a href="javascript:void(0)" class="btn add_mini_game">Add Day</a>');

                if($('.mini_game').length == 1){
                    $('.remove_mini_game').remove();
                }

                if ($('.mini_game').length > 0) {
                    for (var i = 0; i < $('.mini_game').length; i++) {
                        $(".mini_game:nth-child("+(i+1)+")").find('h5').text('Day '+(i+1));
                    }
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