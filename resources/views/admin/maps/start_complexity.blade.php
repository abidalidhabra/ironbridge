@section('title','Ironbridge1779 | Maps')
@extends('admin.layouts.admin-app')
@section('styles')
<style>
    #map {
        height: 500px;
        width: 100%;
    }
    .modalbuttonadd{
      margin:5px;
    }
    
    .activeBorder{
      border: 1px solid #000 !important;
    }
</style>
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="text-right">
            <a href="{{ route('admin.boundary_map',$id) }}" class="btn back-btn">Back</a>
        </div>
        <br/>
        <div class="locationinfobox">
            <form method="POST" id="startComplexity">
                <div class="inerdeta_locat">
                    <h2 class="locatininfobtn"><span>Location Info</span>
                        @if(count($location->hunt_complexities) > 0)
                            <a href="javascript:void(0);" class="btn btn-info btn-md" data-action="remove_stars" data-id='{{ $id }}' data-complexity='{{ $complexity }}'>Clear Clues</a>
                        @endif
                    </h2>
                    @if($location->custom_name)
                        <h3><span>Custom Name :</span> {{ $location->custom_name }}</h3>
                    @endif
                    <h3><span>Place Name :</span> {{ $location->place_name }}</h3>
                    <h3><span>City :</span> {{ $location->city }}</h3>
                    <h3><span>Province :</span> {{ $location->province }}</h3>
                    <h3><span>Country :</span> {{ $location->country }}</h3>
                    <h3 id="totalDistance">
                        <span>Total Distance :</span>
                        {{ $totalDistance }}
                    </h3>
                    {{--<h2>{{$complexitySuf}} Complexity Coordinates</h2>--}}
                    <div class="locatininfoinerbtn">
                        <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>1]) }}" class="btn btn-info btn-md @if($complexity == 1) active_btn @endif @if(in_array(1,$complexityarr)) border_black @endif">
                        1 Star</a>
                    </div>
                    <div class="locatininfoinerbtn">
                        <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>2]) }}" class="btn btn-info btn-md @if($complexity == 2) active_btn @endif @if(in_array(2,$complexityarr)) border_black @endif" >2 Stars</a>
                    </div>
                    <div class="locatininfoinerbtn">
                        <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>3]) }}" class="btn btn-info btn-md @if($complexity == 3) active_btn @endif  @if(in_array(3,$complexityarr)) border_black @endif">3 Stars</a>
                    </div>
                    <div class="locatininfoinerbtn">
                        <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>4]) }}" class="btn btn-info btn-md @if($complexity == 4) active_btn @endif @if(in_array(4,$complexityarr)) border_black @endif">4 Stars</a>
                    </div>
                    <div class="locatininfoinerbtn">
                        <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>5]) }}" class="btn btn-info btn-md @if($complexity == 5) active_btn @endif @if(in_array(5,$complexityarr)) border_black @endif">5 Stars</a>
                    </div>
                    <div class="row" id="game_box">
                        @if(isset($location->hunt_complexities[0]))
                            @forelse ($location->hunt_complexities[0]->hunt_clues as $key => $gamedetails)
                                <div class="game_section{{ $key }} selected_game" id="game_{{ str_replace('.','_',substr($gamedetails->location['coordinates'][0], 0, 12).substr($gamedetails->location['coordinates'][1],0,12)) }}">
                                    <div class="form-group col-md-8">
                                        <label>Game:</label>
                                        <select name="game_id[]" class="form-control" data-id="{{ $key }}">
                                            <option value="">Select game</option>
                                            @forelse($games as $game)
                                                <option value="{{ $game->_id }}" {{ (isset($gamedetails->game_id) && $gamedetails->game_id == $game->_id)?'selected':'' }}>{{ $game->name }}</option>
                                            @empty
                                                <option value="">No game found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label>Game Variations:</label>
                                        <select name="game_variation_id[]" class="form-control">
                                            <?php
                                                $game_variations = $games->where('_id',$gamedetails->game_id)->first();
                                            ?>
                                            @if($game_variations)
                                                @forelse($game_variations->game_variation as $game_variation)
                                                    <option value="{{ $game_variation['_id'] }}" {{ (isset($gamedetails->game_variation_id) && $gamedetails->game_variation_id == $game_variation['_id'])?'selected':'' }}>{{ $game_variation['variation_name'] }}</option>
                                                @empty
                                                @endforelse
                                            @else
                                                <option value="">Select game variation</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        @else
                        @endif
                    </div>
                    <input type="hidden" name="coordinates" id="latitude" value="{{ (!empty($location->hunt_complexities[0]->hunt_clues))? json_encode($cluesCoordinates,true):'' }}">
                    <?php
                        // exit();
                    ?>
                    <input type="hidden" name="hunt_id" value="{{ $location->_id }}">
                    <input type="hidden" name="complexity" value="{{ $complexity }}">
                    <?php
                        // echo "<pre>";
                        // print_r($location->toArray());
                        // exit();
                    ?>
                    <input type="hidden" name="distance" id="distance" value="{{ (count($location->hunt_complexities)>0)?$location->hunt_complexities[0]->distance:'0' }}">
                </div>
                 <div class="customdatatable_box">
                    <div id="map"></div>
                    <br/><br/>
                    <?php if($complexity == 1){ ?>
                        <label>1 Star clues should be 50 meter apart.</label>
                    <?php } else if($complexity == 2){?>
                        <label>2 Stars clues should be 100 meter apart.</label>
                    <?php } else if($complexity == 3){?>
                        <label>3 Stars clues should be 250 meter apart.</label>
                    <?php } else if($complexity == 4){?>
                        <label>4 Stars clues should be 500 meter apart.</label>
                    <?php } else if($complexity == 5){?>
                        <label>5 Stars clues should be 1000 meter apart.</label>
                    <?php }?>
                </div>
                
                <div class="pull-right modal-footer">
                    <button type="submit" class="btn btn-success" id="saveCoordinates">Save</button>
                </div>
            </form>
        </div>
        <br/>
        <br/>
       
    </div>
    <?php
        $boundary = [];
        foreach ($location->boundaries_arr as $key => $value) {
            $boundary [] = [
                            'lat'=>$value[1],
                            'lng'=>$value[0],
                            ];
        }
    ?>
@endsection

@section('scripts')
    
    <script type="text/javascript">
        function initMap() {
            var uluru = { lat: {{ $location->location['coordinates'][1] }} , lng: {{ $location->location['coordinates'][0] }} };
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 18,
                center: uluru,
                mapTypeId: 'terrain',
                scaleControl: true
            });


            
            var triangleCoords = [
                <?php echo json_encode($boundary) ?>
            ];

            
            // var marker = new google.maps.Marker({position: coord, map: map});
            var i=0;
            var icon = {
                url: "{{ asset('admin_assets/images/blue_marker.png') }}",
                scaledSize: new google.maps.Size(20, 32),
            };

            // Construct the polygon.
            var bermudaTriangle = new google.maps.Polygon({
                paths: triangleCoords,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.35
            });
            bermudaTriangle.setMap(map);
            var coordinates = [];
            <?php
                if(count($location->hunt_complexities) > 0){
                    $coord = [];
               
                        foreach($location->hunt_complexities[0]->hunt_clues->pluck('location.coordinates') as $coordinates){
                        $coord [] = [
                                    'lat'=>$coordinates[1],
                                    'lng'=>$coordinates[0],
                                    ];
            
            ?>
                // var coord = ;
                  new google.maps.Marker({
                      position: { lat: {{ $coordinates[1] }} , lng: {{ $coordinates[0] }} },
                      map: map,
                      size:[10,10],
                      icon:icon
                  });
                <?php } ?>

                var coord = [
                            <?php echo json_encode($coord) ?>
                        ];
                // Construct the polygon.
                var starPolygon = new google.maps.Polygon({
                    paths: coord,
                    strokeColor: '#007F7F',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#007F7F',
                    fillOpacity: 0.35,
                    editable: true,
                    draggable: false
                });
                starPolygon.setMap(map);
                starPolygon.addListener('mouseup', function(event){
                    var vertices = this.getPath();
                    // Iterate over the vertices.
                    var boundary_arr = [];
                    var games = <?php echo json_encode($usedGame) ?>;
                    var option_game = "'<option value=''>Select game</option>";
                    var option_game_variation1 = "'<option value=''>Select game variation</option>";

                    /*$.each(games, function(i, k) {
                        option_game += '<option value="'+k._id+'">'+k.name+'</option>';
                    });*/
                    var coordinates = [];
                    // var distance = google.maps.geometry.spherical.computeArea(this.getPath());
                    var distance = google.maps.geometry.spherical.computeLength(this.getPath());
                    $('#distance').val(distance);
                    var totalDistance = Math.round(distance);
                    
                    $('#totalDistance').html('<span>Total Distance :</span> '+parseFloat((totalDistance/1000).toFixed(2))+ 'KM');

                    for (var i =0; i < vertices.getLength(); i++) {


                        var xy = vertices.getAt(i);
                        /*boundary_arr[i] = xy.lng() +','+ xy.lat();*/
                        var arr = [];
                        arr.push(xy.lng());
                        arr.push(xy.lat());
                        coordinates.push(arr);
                        var gameId = xy.lng().toString().slice(0,12)+xy.lat().toString().slice(0,12);
                        if($('#game_'+gameId.replace(/\./g,'_')).length == 0){
                            var random_game = games[Math.floor(Math.random()*games.length)];
                            $.each(games, function(i, k) {
                                var selected = '';
                                if (k._id == random_game._id) {
                                    var selected = 'selected'; 
                                }

                                option_game += '<option value="'+k._id+'" '+selected+'>'+k.name+'</option>';
                            });
                            

                            var random_game_variation = random_game.game_variation[Math.floor(Math.random()*random_game.game_variation.length)];
                            $.each(random_game.game_variation, function(i, k) {
                                var selected1 = '';
                                if (k._id == random_game_variation._id) {
                                    var selected1 = 'selected'; 
                                }

                                option_game_variation1 += '<option value="'+k._id+'" '+ selected1 +'>'+k.variation_name+'</option>';
                            });
                            
                            //GAME SELECT REMOVE
                            games.splice($.inArray(random_game, games),1);

                            $('.selected_game:nth-child('+i+')').after('<div class="game_section'+i+' selected_game" id="game_'+gameId.replace(/\./g,'_')+'">\
                                                    <div class="form-group col-md-8">\
                                                        <label>Game:</label>\
                                                        <select name="game_id[]" data-action="game_id'+i+'" data-id="'+i+'" class="form-control">\
                                                        '+option_game+'</select>\
                                                    </div>\
                                                    <div class="form-group col-md-8">\
                                                        <label>Game Variations:</label>\
                                                        <select name="game_variation_id[]" id="game_variation_id'+i+'" class="form-control">\
                                                            '+option_game_variation1+'\
                                                        </select>\
                                                    <div>\
                                                <div>');

                        } else {
                            // alert('fail');
                        }
                    }
                    $('#latitude').val(JSON.stringify(coordinates));
                });
            <?php 
                } else {
            ?>
                var drawingManager = new google.maps.drawing.DrawingManager({
                        drawingMode: google.maps.drawing.OverlayType.POLYGON,
                        drawingControl: false,
                        drawingControlOptions: {
                            position: google.maps.ControlPosition.TOP_CENTER,
                            drawingModes: ['marker'],
                            icon:icon
                        },
                        polygonOptions: {
                            editable: true,
                            zIndex: 1,
                        }
                    });

                drawingManager.setMap(map);
                /*google.maps.event.addListener(drawingManager, "overlaycomplete", function(e) {
                    if (e.type != google.maps.drawing.OverlayType.MARKER) {
                            drawingManager.setDrawingMode(null);
                            var newShape = e.overlay;
                            newShape.type = e.type;
                            google.maps.event.addListener(newShape, 'click', function() {
                                console.log('success');
                                setSelection(newShape);
                            });

                        var area = google.maps.geometry.spherical.computeArea(newShape.getPath());
                        console.log('area');
                        // var length = google.maps.geometry.spherical.computeLength(newShape.getPath());
                          
                    }
                });*/
                google.maps.event.addListener(drawingManager, "overlaycomplete", function(event){
                    if (event.type != google.maps.drawing.OverlayType.MARKER) {
                            drawingManager.setDrawingMode(null);
                            var newShape = event.overlay;
                            newShape.type = event.type;
                            google.maps.event.addListener(newShape, 'click', function() {
                                setSelection(newShape);
                            });

                        var distance = google.maps.geometry.spherical.computeLength(newShape.getPath());
                        $('#distance').val(distance);
                        var totalDistance = Math.round(distance);
                        
                        $('#totalDistance').html('<span>Total Distance :</span> '+parseFloat((totalDistance/1000).toFixed(2))+ 'KM');
                    }
                    overlayClickListener(event.overlay);
                    // $('#boundary_arr').val(event.overlay.getPath().getArray());
                    var boundary_arr = [];
                    var i=1;
                    event.overlay.getPath().getArray().forEach((value, key) => {
                        var arr = [];
                        arr.push(value.lng());
                        arr.push(value.lat());
                        coordinates.push(arr);
                    });
                    var i;
                    var games = <?php echo json_encode($games) ?>;


                    for (i = 0; i < JSON.stringify(coordinates.length); i++) {
                        var option_game = "'<option value=''>Select game</option>";
                        var option_game_variation = "'<option value=''>Select game variation</option>";
                        var random_game = games[Math.floor(Math.random()*games.length)];
                        $.each(games, function(i, k) {
                            var selected = '';
                            if (k._id == random_game._id) {
                                var selected = 'selected'; 
                            }

                            option_game += '<option value="'+k._id+'" '+selected+'>'+k.name+'</option>';
                        });
                        

                        var random_game_variation = random_game.game_variation[Math.floor(Math.random()*random_game.game_variation.length)];
                        $.each(random_game.game_variation, function(i, k) {
                            var selected1 = '';
                            if (k._id == random_game_variation._id) {
                                var selected1 = 'selected'; 
                            }

                            option_game_variation += '<option value="'+k._id+'" '+ selected1 +'>'+k.variation_name+'</option>';
                        });

                         //GAME SELECT REMOVE
                        games.splice($.inArray(random_game, games),1);

                        var html = '<div class="game_section'+i+'">\
                                        <div class="form-group col-md-8">\
                                            <label>Game:</label>\
                                            <select name="game_id[]" data-action="game_id'+i+'" data-id="'+i+'" class="form-control">\
                                            '+option_game+'</select>\
                                        </div>\
                                        <div class="form-group col-md-8">\
                                            <label>Game Variations:</label>\
                                            <select name="game_variation_id[]" id="game_variation_id'+i+'" class="form-control">\
                                            '+option_game_variation+'</select>\
                                        <div>\
                                    <div>';
                        $('#game_box').append(html);
                    }
                    $('#latitude').val(JSON.stringify(coordinates));
                });
            <?php } ?>
            
            
            function overlayClickListener(overlay) {
                google.maps.event.addListener(overlay, "mouseup", function(event){
                    $('#boundary_arr').val(overlay.getPath().getArray());
                    console.log(overlay.getPath().getArray());
                    console.log(overlay.getPath());
                    var distance = google.maps.geometry.spherical.computeLength(overlay.getPath());
                    $('#distance').val(distance);
                    var totalDistance = Math.round(distance);
                    $('#totalDistance').html('<span>Total Distance :</span> '+parseFloat((totalDistance/1000).toFixed(2))+ 'KM');
                });

            }

        }

        //GAME 
        $(document).on("change","[name='game_id[]']",function() {
            var game_id = $(this).val();
            var id = $(this).data('id');
            $.ajax({
                type: "get",
                url: '{{ route("admin.getGameVariations") }}',
                data: { 'game_id':game_id,'array_id':id },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response)
                {
                    if (response.status == true) {
                        //toastr.success(response.message);
                        // $('[name="game_variation_id"]').html('');
                        $('.game_section'+response.array_id).find('[name="game_variation_id[]"]').html('');
                        if (response.data.length > 0) {
                            $.each(response.data, function( index, value ) {
                                var html = "<option value='"+value._id+"'>"+value.variation_name+"</option>"
                                // $('[name="game_variation_id"]').append(html);
                                $('.game_section'+response.array_id).find('[name="game_variation_id[]"]').append(html);
                                console.log($('.game_section'+response.array_id).length)
                            });
                        } else {
                            $('[name="game_variation_id"]').append('<option value="">No data found</option>');
                        }
                    } else {
                        toastr.warning(response.message);
                    }
                }
            });
        });

        $('#startComplexity').submit(function(e){
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: '{{ route("admin.storeStarComplexity") }}',
                data: $('#startComplexity').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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

        });

        //CLEAR CLUES
        $('a[data-action="remove_stars"]').click(function(e){
            var complexity = $(this).data('complexity');
            var id = $(this).data('id');
            $.ajax({
                type: "get",
                url: '{{ route("admin.removeStar") }}',
                data: { 'id':id , 'complexity':complexity},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
        });
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0AzhRBk1LARqw9SDz9qwpAkTYDaQNe6o&libraries=drawing&callback=initMap">
    </script>
@endsection