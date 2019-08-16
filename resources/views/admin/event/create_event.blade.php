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
                <h3>Add Event</h3>
            </div>
            <div class="col-md-6 text-right modalbuttonadd">
                <a href="{{ route('admin.event.index') }}" class="btn btn-info btn-md">Back</a>
            </div>
        </div>
    </div>
    <br/><br/>
    <div class="customdatatable_box">
        <form method="POST" id="addEventForm" enctype="multipart/form-data">
            @csrf
             <div class="daingaemtitlebox">
                <h4>Basic Details</h4>
            </div>
            <div class="allbasicdirmain">                
                <div class="allbasicdirbox">                
                   
                    <div class="form-group col-md-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Enter the name">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-control">
                            <option>Select type</option>
                            <option value="single">Single</option>
                            <option value="multi">Multi</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Coin Type</label>
                        <select name="coin_type" class="form-control">
                            <option>Select coin type</option>
                            <option value="ar">Ar</option>
                            <option value="physical">Physical</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">City</label>
                        <select name="city_id" class="form-control">
                            <option>Select city</option>
                            @forelse($cities as $key=>$city)
                            <option value="{{ $city->_id }}">{{ $city->name }}</option>
                            @empty
                            <option>Record Not found</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="text" name="event_start_date" class="form-control datetimepicker" placeholder="Enter the start date">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="text" name="event_end_date" class="form-control datetimepicker" placeholder="Enter the date">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Rejection Ratio<small> In percentage</small></label>
                        <input type="text" name="rejection_ratio" class="form-control" placeholder="Enter the rejection ratio">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Winning Ratio<small> In fix amount</small></label>
                        <input type="text" name="winning_ratio" class="form-control" id="winning_ratio" placeholder="Enter the winning ratio">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Fees</label>
                        <input type="text" name="fees" class="form-control" placeholder="Enter the fees">
                    </div>
                </div>
            </div>
            <!-- <div class="form-group col-md-6">
                <label class="form-label">Place Name</label>
                <input type="text" name="place_name" class="form-control" id="place_name" placeholder="Enter the place name">
            </div> -->
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
             <!-- <div class="col-md-12">
                <div id="map"></div>
            </div> -->
            <!-- GAME DETAILS START CODE -->
            <div class="col-md-12">
                <h4>Mini Game Details</h4>
            </div>
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

            <!-- HUNT CLUE START CODE -->
            <div class="daingaemtitlebox">
                <h4>Hunt Details</h4>
            </div>
            <div class="allbasicdirmain">                
                <div class="allbasicdirbox"> 
                    <div class="form-group col-md-4">
                        <label class="form-label">Map Reveal Date</label>
                        <input type="text" name="map_reveal_date" class="form-control datetimepicker" placeholder="Enter the map reveal date">
                    </div>
                </div>
            </div>
            <!-- END HUNT CLUE  -->



           

            <div class="form-group col-md-12">
                <button type="submit" class="btn btn-success btnSubmit">Submit</button>
            </div>
    </form>
    </div>
</div>


@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0AzhRBk1LARqw9SDz9qwpAkTYDaQNe6o&libraries=places,drawing"></script>
     
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
        /*$(document).on('change','select[name="type"]',function(){
            if($(this).val() == 'single'){
            
            } else {
            
            }
        })*/


        /* SUBMIT FORM */
        $(document).on('submit','#addEventForm',function(e){
            e.preventDefault();
            formData = new FormData($(this)[0]);
            $.ajax({
                type:'POST',
                url:'{{ route("admin.event.store") }}',
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
        })


        // initialize();
        // function initialize() {
        //     //static coordinates
        //     var map = new google.maps.Map(document.getElementById('map'), {
        //         center: {lat: 51.048615, lng: -114.070847 },
        //         zoom: 18,
        //         scaleControl: true
        //     });

                           
        //     //set the autocomplete
        //     var input = document.getElementById('place_name');
        //     var autocomplete = new google.maps.places.Autocomplete(input);
        //      autocomplete.bindTo('bounds', map);

        //     // Set the data fields to return when the user selects a place.
        //     autocomplete.setFields(
        //         ['address_components', 'geometry', 'icon', 'name']);

        //     var marker = new google.maps.Marker({
        //         map: map,
        //         anchorPoint: new google.maps.Point(0, -29)
        //     });

        //     //change listener on each autocomplete action
        //     autocomplete.addListener('place_changed', function(){
        //         //infowindow.close();
        //         marker.setVisible(false);
        //         var place = autocomplete.getPlace();
        //         console.log(place);
        //         if (!place.geometry) {
        //             // User entered the name of a Place that was not suggested and
        //             // pressed the Enter key, or the Place Details request failed.
        //             window.alert("No details available for input: '" + place.name + "'");
        //             return;
        //         }

        //         // If the place has a geometry, then present it on a map.
        //         if (place.geometry.viewport) {
        //             map.fitBounds(place.geometry.viewport);
        //         } else {
        //             map.setCenter(place.geometry.location);
        //             map.setZoom(17);  // Why 17? Because it looks good.
        //         }

        //         marker.setPosition(place.geometry.location);
        //         marker.setVisible(true);

        //         var address = '';
        //         if (place.address_components) {
        //             address = [
        //                 (place.address_components[0] && place.address_components[0].short_name || ''),
        //                 (place.address_components[1] && place.address_components[1].short_name || ''),
        //                 (place.address_components[2] && place.address_components[2].short_name || '')
        //             ].join(' ');
        //         }

        //         /*infowindowContent.children['place-icon'].src = place.icon;
        //         infowindowContent.children['place-name'].textContent = place.name;
        //         infowindowContent.children['place-address'].textContent = address;
        //         infowindow.open(map, marker);*/
        //         placeInfo = getPlaceInformation(place);
        //         $('#latitude').val(placeInfo['latitude']);
        //         $('#longitude').val(placeInfo['longitude']);
        //         // $('#country').val(placeInfo['country']);
        //         // $('#province').val(placeInfo['state']);
        //         console.log(placeInfo['city']);
        //         $('#city').val(placeInfo['city']);
        //     });

           

        //     //update the street view on dragging of marker
        //     google.maps.event.addListener(marker, 'dragend', function (event) {
        //         var newPosition = marker.getPosition();
        //         geocodePosition(newPosition);
        //     });

            

        //     //DRAWING MANAGE
        //     var selectedShape;

        //     var drawingManager = new google.maps.drawing.DrawingManager({
        //         drawingMode: google.maps.drawing.OverlayType.POLYGON,
        //             drawingControl: false,
        //             drawingControlOptions: {
        //                 position: google.maps.ControlPosition.TOP_CENTER,
        //             drawingModes: [google.maps.drawing.OverlayType.POLYGON]
        //         },
        //         polygonOptions: {
        //             editable: true
        //         }
        //     });

            

        //     drawingManager.setMap(map);

        //     var coordinates = [];
        //     var all_overlays = [];

        //     google.maps.event.addListener(drawingManager, "overlaycomplete", function(event){
                
        //         // overlayClickListener(event.overlay);
        //         // $('#boundary_arr').val(event.overlay.getPath().getArray());
        //         all_overlays.push(event);
        //         if (event.type != google.maps.drawing.OverlayType.MARKER) {
        //             // Switch back to non-drawing mode after drawing a shape.
        //             drawingManager.setDrawingMode(null);
        //             var newShape = event.overlay;
        //             newShape.type = event.type;
        //             google.maps.event.addListener(newShape, 'click', function() {
        //               setSelection(newShape);
        //             });
        //             setSelection(newShape);
        //         }



        //         var boundary_arr = [];
        //         var i=1;
        //         event.overlay.getPath().getArray().forEach((value, key) => {
        //             // boundary_arr[i] = value.lng() +','+ value.lat();
        //             // i++;
        //             var arr = [];
        //             arr.push(value.lng());
        //             arr.push(value.lat());
        //             coordinates.push(arr);
        //         });
        //         console.log(coordinates);
        //         $('#boundary_arr').val(JSON.stringify(coordinates));

        //         // console.log(jQuery.parseJSON(boundary_arr));

        //         console.log(event.overlay.getPath().getArray())
        //         //Options
        //         var options = {
        //             path: event.overlay.getPath().getArray(),
        //             strokeColor: "#222",
        //             strokeOpacity: 1,
        //             strokeWeight: 2,
        //             fillColor: "#000",
        //             fillOpacity: 0,
        //             zIndex: 0
        //         }
        //         //Create polygon
        //         var polygon = new google.maps.Polygon(options);

        //         polygon.setMap(map);
        //         //rectangle
        //         if(!google.maps.Polygon.prototype.getBounds)
        //             google.maps.Polygon.prototype.getBounds = function() {
        //             var bounds = new google.maps.LatLngBounds();
        //             var paths = this.getPaths();    
        //             for (var i = 0; i < paths.getLength(); i++) {
        //                 var path = paths.getAt(i);
        //                 for (var j = 0; j < path.getLength(); j++) {
        //                     bounds.extend(path.getAt(j));
        //                 }
        //             }
        //             return bounds;
        //         }

        //         var rectangle = new google.maps.Rectangle({
        //             strokeColor: '#FF0000',
        //             strokeOpacity: 0.8,
        //             strokeWeight: 2,
        //             fillColor: '#FFF',
        //             fillOpacity: 0.35,
        //             map: map,
        //             bounds: polygon.getBounds()
        //         });
        //         var boundary_box = [];
        //         var j=1;
        //         $('#boundary_box').val(JSON.stringify(polygon.getBounds()));
        //     });
        // }
        // function getPlaceInformation(place){
        //     placeInfo = [];
        //     placeInfo['latitude'] = "";
        //     placeInfo['longitude'] = "";
        //     placeInfo['latitude'] = place.geometry.location.lat();
        //     placeInfo['longitude'] = place.geometry.location.lng();
        //     $.each(place.address_components,function(index,value){
        //         if(value.types[0] == 'locality' || value.types[0] == 'administrative_area_level_3'){
        //             placeInfo['city'] = value['long_name'];
        //         }
        //     });
        //     return placeInfo;
        // } 
        
    </script>
@endsection