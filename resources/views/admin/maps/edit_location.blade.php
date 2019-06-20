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
                    <h3>Edit location</h3>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('admin.mapsList') }}" class="btn back-btn">Back</a>
                </div>
            </div>
        </div>
        <br/>
        <br/>
        <div class="customdatatable_box">
            <form method="post" id="editLocationForm">
                @csrf
                <div class="modal-body">
                    <div class="modalbodysetbox">
                        <div class="form-group">
                            <label class="control-label">Custom Name:</label>
                            <input type="text" class="form-control" placeholder="Enter custom name" name="name" value="{{ $location->name }}" id="custom_name">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Place Name:</label>
                            <input type="text" class="form-control" placeholder="Enter place name" name="place_name" id="place_name" autocomplete="off" value="{{ $location->place_name }}" readonly="">
                            <input type="hidden" name="id" value="{{ $location->_id }}" />
                            <input type="hidden" name="latitude" id="latitude" value="{{ $location->location['coordinates'][1] }}" />
                            <input type="hidden" name="longitude" id="longitude" value="{{ $location->location['coordinates'][0] }}" />
                        </div>
                        <div class="form-group">
                            <label class="control-label">City:</label>
                            <input type="text" class="form-control" placeholder="Enter city name" name="city" id="city" value="{{ $location->city }}" readonly="">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Province:</label>
                            <input type="text" class="form-control" placeholder="Enter province name" name="province" id="province" value="{{ $location->province }}" readonly="">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Country:</label>
                            <input type="text" class="form-control" id="country" placeholder="Enter country name" name="country" value="{{ $location->country }}" readonly="">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Challenge Entry Fee (In gold currency):</label>
                            <input type="number" class="form-control" id="fees" placeholder="Enter fees name" name="fees" value="{{ $location->fees }}" >
                        </div>
                        <input type="hidden" name="boundaries_arr" value="" id="boundary_arr"  />
                        <!-- <input type="hidden" name="boundary_box" value="" id="boundary_box"  /> -->
                        <div id="map"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-danger btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <?php
        $boundary = [];
        foreach ($location->boundaries_arr as $key => $value) {
            $boundary [] = [
                                'lat'=>$value[1],
                                'lng'=>$value[0],
                            ];
        }

        foreach ($location->boundingbox as $key => $value) {
            if ($key == 0) {
                $boundingbox['north'] = (float)$value; 
            }
            if ($key == 2) {
                $boundingbox['south'] = (float)$value; 
            }
            if ($key == 3) {
                $boundingbox['east'] = (float)$value; 
            }
            if ($key == 1) {
                $boundingbox['west'] = (float)$value; 
            }
        }
    ?>

@endsection

@section('scripts')
    <!-- <script type="text/javascript" src="{{ asset('js/toastr.min.js') }}"></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0AzhRBk1LARqw9SDz9qwpAkTYDaQNe6o&libraries=places,drawing"></script>
    

    <script type="text/javascript">
        //$(document).ready(function() {
            $('.btn-cancel').click(function(){
                location.reload();
            })

            function initMap() {
                var uluru = { lat: {{ $location->location['coordinates'][1] }} , lng: {{ $location->location['coordinates'][0] }} };
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 16,
                    center: uluru,
                    mapTypeId: 'terrain'
                });

                
                var triangleCoords = [
                    <?php echo json_encode($boundary) ?>
                ];

                var marker = new google.maps.Marker({position: uluru, map: map});

                // Construct the polygon.
                var polygon = new google.maps.Polygon({
                    paths: triangleCoords,
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#FF0000',
                    fillOpacity: 0.35,
                    editable: true,
                    draggable: false
                });

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
                  bounds: <?php echo json_encode($boundingbox); ?>,
                  editable: true
                });
                
                polygon.setMap(map);

                polygon.addListener('mouseup', showArrays);
                // polygon.addListener('click', showArrays);
                
                //RECTANGLE
                //rectangle.setMap(map);
                
            }

            function showArrays(event) {
                var vertices = this.getPath();
                // Iterate over the vertices.
                var boundary_arr = [];
                for (var i =0; i < vertices.getLength(); i++) {
                    var xy = vertices.getAt(i);
                    //boundary_arr[i] = xy.lng() +','+ xy.lat();
                    var arr = [];
                    arr.push(xy.lng());
                    arr.push(xy.lat());
                    boundary_arr.push(arr);
                }
                console.log(JSON.stringify(boundary_arr));
                $('#boundary_arr').val(JSON.stringify(boundary_arr));
            }
           
            initMap();
        //});
        //ADD LOCATION
        $('#editLocationForm').submit(function(e) {
                e.preventDefault();
            })
        .validate({
            focusInvalid: false, 
            ignore: "",
            rules: {
                name: { required: true },
                place_name: { required: true },
                city: { required: true },
                province: { required: true },
                fees: { required: true },
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                let routeName = "{{ route('admin.boundary_map',['id' => 'targetValue']) }}";
                $.ajax({
                    type: "POST",
                    url: '{{ route("admin.update_location") }}',
                    data: formData,
                    processData:false,
                    cache:false,
                    contentType: false,
                    success: function(response)
                    {
                        if (response.status == true) {
                            toastr.success(response.message);
                            //routeName = routeName.replace("targetValue", response.id);
                            //location.replace(routeName);
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            }
        });
    </script>
    
@endsection