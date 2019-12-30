@section('title','Ironbridge1779 | Maps')
@extends('admin.layouts.admin-app')
@section('styles')
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
    <style type="text/css">
        #map {
            height: 500px;
            width: 100%;
          }
    </style>
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="users_datatablebox">
            <div class="">               
                <div class="col-md-12 text-right">
                    <a href="{{ route('admin.mapsList') }}" class="btn back-btn">Back</a>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <h3>Add location</h3>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <br/>
        <div class="customdatatable_box">
            <form method="post" id="addNewsForm">
                @csrf
                <div class="modal-body padboxset">
                    <div class="modalbodysetbox">
                        <div class="form-group">
                            <label class="control-label">Name:</label>
                            <input type="text" class="form-control" placeholder="Enter custom name" name="name" id="name">
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="google_location" id="google_location" value="true" checked>Google Location</label>
                        </div>
                        <div class="form-group placenameBox">
                            <label class="control-label">Place Name:</label>
                            <input type="text" class="form-control" placeholder="Enter place name" name="place_name" id="place_name" autocomplete="off">
                            <!-- <input type="hidden" name="latitude" id="latitude"/>
                            <input type="hidden" name="longitude" id="longitude"/> -->
                        </div>
                        <div class="form-group row latlongBox" style="display: none;">
                            <div class="col-md-6">
                                <label class="control-label">Latitude:</label>
                                <input type="number" class="form-control" placeholder="Enter latitude" name="latitude" id="latitude"/>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Longitude:</label>
                                <input type="number" class="form-control"  placeholder="Enter longitude" name="longitude" id="longitude" />
                            </div>
                            <div class="col-md-12">
                                <input id="submit" type="button" value="Reverse Geocode">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label">City:</label>
                            <input type="text" class="form-control" placeholder="Enter city name" name="city" id="city">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Province:</label>
                            <input type="text" class="form-control" placeholder="Enter province name" name="province" id="province">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Country:</label>
                            <input type="text" class="form-control" id="country" placeholder="Enter country name" name="country">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Challenge Entry Fee (In gold currency):</label>
                            <input type="number" class="form-control" id="fees" placeholder="Enter fees name" name="fees">
                        </div>
                        <input type="hidden" name="boundary_arr" value="" id="boundary_arr"  />
                        <input type="hidden" name="boundary_box" value="" id="boundary_box"  />
                        <div id="map" style="height: 500px;width: 100%;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-danger btn-cancel">Cancel</button>
                    <input type="button" id="resetPolygon" value="Reset" style="display: none;" />
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <!-- <script type="text/javascript" src="{{ asset('js/toastr.min.js') }}"></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0AzhRBk1LARqw9SDz9qwpAkTYDaQNe6o&libraries=places,drawing"></script>
    

    <script type="text/javascript">
        $(document).ready(function() {

            $('.btn-cancel').click(function(){
                location.reload();
            })

            $(document).on('change','#google_location',function(){
                if ($(this).is(':checked')) {
                    $('#place_name').val('');
                    $('.latlongBox').hide();
                    $('.placenameBox').show();
                } else {
                    $('#latitude , #longitude , #place_name').val('');
                    $('.placenameBox').hide();
                    $('.latlongBox').show();
                }
            })

            //ADD LOCATION
            $('#place_name').focus(function() {
                $(this).attr('autocomplete', 'new-placeSearch');
            });

            var latitude = 51.048615; 
            var longitude = -114.070847;
            $("#latitude , #longitude").focusout(function(){
               if (($('#latitude').val()!="" && $('#latitude').val() != 51.048615) && ($('#longitude').val()!="" && $('#longitude').val() != -114.070847)) {

               }
            });

            /*$("#longitude").focus(function(){
               longitude = $('#longitude').val();
                initialize();
            });*/


            function initialize() {
                //static coordinates
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {lat: latitude, lng: longitude },
                    zoom: 18,
                    scaleControl: true
                });

                 /* LAT LONG BASED ADDRESS FIND */
                var geocoder = new google.maps.Geocoder;
                var infowindow = new google.maps.InfoWindow;
                document.getElementById('submit').addEventListener('click', function() {
                    var lat = parseFloat(document.getElementById("latitude").value);
                    var lng = parseFloat(document.getElementById("longitude").value);
                    var latlng = new google.maps.LatLng(lat, lng);
                    var geocoder = geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ 'latLng': latlng }, function (results, status) {
                        var marker = new google.maps.Marker({
                            position: latlng,
                            map: map
                          });
                        infowindow.setContent(results[0].formatted_address);
                        infowindow.open(map, marker);
                       
                        for (var i = 0; i < results.length; i++) {
                            if (results[i].types[0] === "locality") {
                                var city = results[i].address_components[0].short_name;
                                var state = results[i].address_components[2].long_name;
                                var country = results[i].address_components[3].long_name;
                                $('#city').val(city);
                                $('#province').val(state);
                                $('#country').val(country);
                                console.log(results[i].address_components);
                            }
                        }

                    });
                });
                
                //set the autocomplete
                var input = document.getElementById('place_name');
                var autocomplete = new google.maps.places.Autocomplete(input);
                 autocomplete.bindTo('bounds', map);

                // Set the data fields to return when the user selects a place.
                autocomplete.setFields(
                    ['address_components', 'geometry', 'icon', 'name']);

                /*var infowindow = new google.maps.InfoWindow();
                var infowindowContent = document.getElementById('infowindow-content');
                infowindow.setContent(infowindowContent);*/
                var marker = new google.maps.Marker({
                  map: map,
                  anchorPoint: new google.maps.Point(0, -29)
                });

                //change listener on each autocomplete action
                autocomplete.addListener('place_changed', function(){
                    //infowindow.close();
                    marker.setVisible(false);
                    var place = autocomplete.getPlace();
                    console.log(place);
                    if (!place.geometry) {
                        // User entered the name of a Place that was not suggested and
                        // pressed the Enter key, or the Place Details request failed.
                        window.alert("No details available for input: '" + place.name + "'");
                        return;
                    }

                    // If the place has a geometry, then present it on a map.
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);  // Why 17? Because it looks good.
                    }

                    marker.setPosition(place.geometry.location);
                    marker.setVisible(true);

                    var address = '';
                    if (place.address_components) {
                        address = [
                            (place.address_components[0] && place.address_components[0].short_name || ''),
                            (place.address_components[1] && place.address_components[1].short_name || ''),
                            (place.address_components[2] && place.address_components[2].short_name || '')
                        ].join(' ');
                    }

                    /*infowindowContent.children['place-icon'].src = place.icon;
                    infowindowContent.children['place-name'].textContent = place.name;
                    infowindowContent.children['place-address'].textContent = address;
                    infowindow.open(map, marker);*/
                    placeInfo = getPlaceInformation(place);
                    $('#latitude').val(placeInfo['latitude']);
                    $('#longitude').val(placeInfo['longitude']);
                    $('#country').val(placeInfo['country']);
                    $('#province').val(placeInfo['state']);
                    $('#city').val(placeInfo['city']);
                });

               

                //update the street view on dragging of marker
                google.maps.event.addListener(marker, 'dragend', function (event) {
                    var newPosition = marker.getPosition();
                    geocodePosition(newPosition);
                });

                

                //DRAWING MANAGE
                var selectedShape;

                var drawingManager = new google.maps.drawing.DrawingManager({
                    drawingMode: google.maps.drawing.OverlayType.POLYGON,
                        drawingControl: false,
                        drawingControlOptions: {
                            position: google.maps.ControlPosition.TOP_CENTER,
                        drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                    },
                    polygonOptions: {
                        editable: true
                    }
                });

                

                drawingManager.setMap(map);


                /*google.maps.event.addListener(drawingManager, "overlaycomplete", function(event) {
                    var newShape = event.overlay;
                    newShape.type = event.type;
                });*/
                
                var coordinates = [];
                var all_overlays = [];

                function clearSelection() {
                    if (selectedShape) {
                        selectedShape.setEditable(false);
                        selectedShape = null;
                    }
                }
                function setSelection(shape) {
                    clearSelection();
                    selectedShape = shape;
                    shape.setEditable(true);
                }

                google.maps.event.addListener(drawingManager, "overlaycomplete", function(event){
                    
                    // overlayClickListener(event.overlay);
                    // $('#boundary_arr').val(event.overlay.getPath().getArray());
                    all_overlays.push(event);
                      if (event.type != google.maps.drawing.OverlayType.MARKER) {
                        // Switch back to non-drawing mode after drawing a shape.
                        drawingManager.setDrawingMode(null);

                        // Add an event listener that selects the newly-drawn shape when the user
                        // mouses down on it.
                        var newShape = event.overlay;
                        newShape.type = event.type;
                        google.maps.event.addListener(newShape, 'click', function() {
                          setSelection(newShape);
                        });
                        setSelection(newShape);
                      }



                    var boundary_arr = [];
                    var i=1;
                    event.overlay.getPath().getArray().forEach((value, key) => {
                        // boundary_arr[i] = value.lng() +','+ value.lat();
                        // i++;
                        var arr = [];
                        arr.push(value.lng());
                        arr.push(value.lat());
                        coordinates.push(arr);
                    });
                    console.log(coordinates);
                    $('#boundary_arr').val(JSON.stringify(coordinates));

                    // console.log(jQuery.parseJSON(boundary_arr));

                    console.log(event.overlay.getPath().getArray())
                    //Options
                    var options = {
                        path: event.overlay.getPath().getArray(),
                        strokeColor: "#222",
                        strokeOpacity: 1,
                        strokeWeight: 2,
                        fillColor: "#000",
                        fillOpacity: 0,
                        zIndex: 0
                    }
                    //Create polygon
                    var polygon = new google.maps.Polygon(options);

                    polygon.setMap(map);
                    //rectangle
                    if(!google.maps.Polygon.prototype.getBounds)
                        google.maps.Polygon.prototype.getBounds = function() {
                        var bounds = new google.maps.LatLngBounds();
                        var paths = this.getPaths();    
                        for (var i = 0; i < paths.getLength(); i++) {
                            var path = paths.getAt(i);
                            for (var j = 0; j < path.getLength(); j++) {
                                bounds.extend(path.getAt(j));
                            }
                        }
                        return bounds;
                    }

                    var rectangle = new google.maps.Rectangle({
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#FFF',
                        fillOpacity: 0.35,
                        map: map,
                        bounds: polygon.getBounds()
                    });
                    var boundary_box = [];
                    var j=1;
                    // $('#boundary_box').val(polygon.getBounds());
                    $('#boundary_box').val(JSON.stringify(polygon.getBounds()));
                    //$('#resetPolygon').show();
                });
                google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
                    //$('#resetPolygon').show();
                    //alert('hello');
                });
                /*$('#resetPolygon').click(function() {
                    if (selectedShape) {
                        selectedShape.setMap(null);
                    }
                    drawingManager.setMap(null);
                });*/
                

            }    
            function getPlaceInformation(place){
                placeInfo = [];
                placeInfo['latitude'] = "";
                placeInfo['longitude'] = "";
                placeInfo['street'] = "";
                placeInfo['city'] = "";
                placeInfo['state'] = "";
                placeInfo['postalCode'] = "";
                placeInfo['country'] = "";

                placeInfo['name'] = place.name;
                placeInfo['latitude'] = place.geometry.location.lat();
                placeInfo['longitude'] = place.geometry.location.lng();
                $.each(place.address_components,function(index,value){
                    if(value.types[0] == 'postal_code'){
                        placeInfo['postalCode'] = value['long_name'];
                    }else if(value.types[0] == 'locality' || value.types[0] == 'administrative_area_level_3'){
                        placeInfo['city'] = value['long_name'];
                    }else if(value.types[0] == 'administrative_area_level_1'){
                        placeInfo['state'] = value['long_name'];
                    }else if(value.types[0] == 'street_number1'){
                        placeInfo['street'] = value['long_name'];
                    }else if(value.types[0] == 'country'){
                        placeInfo['country'] = value['long_name'];
                    }
                });
                return placeInfo;
            }
            function overlayClickListener(overlay) {
                google.maps.event.addListener(overlay, "mouseup", function(event){
                    $('#boundary_arr').val(overlay.getPath().getArray());
                    console.log(overlay.getPath().getArray());
                    console.log(overlay.getPath());
                });
            }
            initialize();


            //ADD LOCATION
            $('#addNewsForm').submit(function(e) {
                    e.preventDefault();
                })
            .validate({
                focusInvalid: false, 
                ignore: "",
                rules: {
                    name: { required: true },
                    place_name:{
                        required:function(){
                            var google_location = $("#google_location").is(":checked");
                            if(google_location == true){
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                    // place_name: { required: true },
                    city: { required: true },
                    province: { required: true },
                    country: { required: true },
                    fees: { required: true },
                    latitude: { required: true },
                    longitude: { required: true },
                },
                submitHandler: function (form) {
                    var formData = new FormData(form);
                    let routeName = "{{ route('admin.boundary_map',['id' => 'targetValue']) }}";
                    $.ajax({
                        type: "POST",
                        url: '{{ route("admin.store_location") }}',
                        data: formData,
                        processData:false,
                        cache:false,
                        contentType: false,
                        success: function(response)
                        {
                            if (response.status == true) {
                                toastr.success(response.message);
                                routeName = routeName.replace("targetValue", response.id);
                                location.replace(routeName);
                            } else {
                                toastr.warning(response.message);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection