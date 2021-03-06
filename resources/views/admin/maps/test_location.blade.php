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
                <div class="col-md-6">
                    <h3>Add location</h3>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('admin.mapsList') }}" class="btn back-btn">Back</a>
                </div>
            </div>
        </div>
        <br/>
        <br/>
        <div class="customdatatable_box">
            <form method="post" id="addNewsForm">
                @csrf
                <div class="modal-body">
                    <div class="modalbodysetbox">
                        <div class="form-group">
                            <label class="control-label">Custom Name:</label>
                            <input type="text" class="form-control" placeholder="Enter custom name" name="custom_name" id="custom_name">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Place Name:</label>
                            <input type="text" class="form-control" placeholder="Enter place name" name="place_name" id="place_name" autocomplete="off">
                            <input type="hidden" name="latitude" id="latitude"/>
                            <input type="hidden" name="longitude" id="longitude"/>
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
                        <input type="hidden" name="boundary_arr" value="" id="boundary_arr"  />
                        <input type="hidden" name="boundary_box" value="" id="boundary_box"  />
                        <div id="map" style="height: 500px;width: 100%;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-danger btn-cancel">Cancel</button>
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

            //ADD LOCATION
            $('#place_name').focus(function() {
                $(this).attr('autocomplete', 'new-placeSearch');
            });


            function initialize() {
                //static coordinates
                var map = new google.maps.Map(document.getElementById('map'), {
                  center: {lat: 51.048615, lng: -114.070847 },
                  zoom: 18,
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
                    console.log(placeInfo);
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
                var polyOptions = {
                    strokeWeight: 0,
                    fillOpacity: 0.45,
                    editable: true
                };

                var drawingManager = new google.maps.drawing.DrawingManager({
                    /*drawingMode: google.maps.drawing.OverlayType.POLYGON,
                        drawingControl: false,
                        drawingControlOptions: {
                            position: google.maps.ControlPosition.TOP_CENTER,
                        drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                    },
                    polygonOptions: {
                        editable: true
                    }*/
                     drawingMode: google.maps.drawing.OverlayType.POLYGON,
                    markerOptions: {
                      draggable: true
                    },
                    polylineOptions: {
                      editable: true
                    },
                    rectangleOptions: polyOptions,
                    circleOptions: polyOptions,
                    polygonOptions: polyOptions,
                    map: map
                });

               

                
                //var outlineMarkers = new Array();
                // google.maps.event.addListener(map, "click", function (event) {
                    
                //     //event.preventDefault();
                //     var markerIndex = polyLine.getPath().length;
                //     polyLine.setMap(map);
                //     var isFirstMarker = markerIndex === 0;
                //     var icon = {
                //         url: "{{ asset('admin_assets/images/blue_marker.png') }}",
                //         scaledSize: new google.maps.Size(20, 32),
                //     };
                //     var marker = new google.maps.Marker({
                //         map: map,
                //         position: event.latLng,
                //         draggable: true,
                //         icon:icon
                //     });
                    
                //     if (isFirstMarker) {
                //         google.maps.event.addListener(marker, 'click', function () {
                //         console.log(isFirstMarker);
                //             var path = polyLine.getPath();
                //             polyGon.setPath(path);
                //             polyGon.setMap(Map);
                //         });
                //     } else {
                //         //    marker.setIcon(yellowIcon);
                //     }
                    
                    
                //     google.maps.event.addListener(polyLine, 'click', function(clickEvent){
                //         //did you want to do something here??
                //     });
                    
                //     polyLine.getPath().push(event.latLng);
                    
                //     //different colored markers so user can tell which was the first marker the placed
                //     //if(markerIndex > 1)
                //     //    outlineMarkers[markerIndex-1].setIcon(blueIcon);
                    
                //     outlineMarkers.push(marker);
                            
                //     google.maps.event.addListener(marker, 'drag', function (dragEvent) {
                //         polyLine.getPath().setAt(markerIndex, dragEvent.latLng);
                //         updateDistance(outlineMarkers);
                //     });

                            
                //     updateDistance(outlineMarkers);
                // }); 

                // drawingManager.setMap(map);

                //drawingManager.setMap(map);
                google.maps.event.addListener(drawingManager, "overlaycomplete", function(e) {
                    if (e.type != google.maps.drawing.OverlayType.MARKER) {
                      // Switch back to non-drawing mode after drawing a shape.
                      drawingManager.setDrawingMode(null);

                      // Add an event listener that selects the newly-drawn shape when the user
                      // mouses down on it.
                      var newShape = e.overlay;
                      newShape.type = e.type;
                      google.maps.event.addListener(newShape, 'click', function() {
                        setSelection(newShape);
                      });

                      var area = google.maps.geometry.spherical.computeArea(newShape.getPath());
                      var length = google.maps.geometry.spherical.computeLength(newShape.getPath());
                      //document.getElementById("area").innerHTML = "Area =" + area;
                      //setSelection(newShape);
                      alert(area);
                      alert(length);
                    }
                });

                google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
                    //get the coordinate array of your polygon
                    var path = polygon.getPath();
                    //calculate area
                    var area = google.maps.geometry.spherical.computeArea(path);
                    //calculate length
                    var length = google.maps.geometry.spherical.computeLength(path);
                    //print the area & length
                    console.log('Polygon Area: ' +  area/1000000 + ' km sqs');
                    console.log('Polygon Length: ' +  length/1000 + ' kms');
                });

                google.maps.event.addListener(drawingManager, "overlaycomplete", function(event){
                    overlayClickListener(event.overlay);
                    // $('#boundary_arr').val(event.overlay.getPath().getArray());
                    var boundary_arr = [];
                    var i=1;
                    event.overlay.getPath().getArray().forEach((value, key) => {
                        boundary_arr[i] = value.lng() +','+ value.lat();
                        i++;
                    });
                    $('#boundary_arr').val(JSON.stringify(boundary_arr));

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
                    $('#boundary_box').val(polygon.getBounds());
                });
            }    

            function updateDistance(outlineMarkers){
                var totalDistance = 0.0;
                    var last = undefined;
                    var sumDistance = sumDistance;
                    $.each(outlineMarkers, function(index, val){
                        if(last){
                            //console.log(google.maps.geometry.spherical.computeDistanceBetween(last.position, val.position));
                            totalDistance += google.maps.geometry.spherical.computeDistanceBetween(last.position, val.position);
                        }
                        last = val;
                    });
                        console.log(totalDistance);
                    // sumDistance += totalDistance;
                    // console.log(sumDistance);
                    // console.log(totalDistance);
                    // $("#txtDistance").val(totalDistance);
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
                    custom_name: { required: true },
                    place_name: { required: true },
                    city: { required: true },
                    province: { required: true },
                    country: { required: true },
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