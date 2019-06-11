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
            <div class="inerdeta_locat">
                <h2 class="locatininfobtn"><span>Location Info</span>
                    @if(count($location->complexities) > 0)
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
                <input type="hidden" name="coordinates[]" id="latitude">
            </div>
             <div class="customdatatable_box">
                <div id="map"></div>
            </div>
            <div class="pull-right modal-footer">
                    <button type="button" class="btn btn-success" id="saveCoordinates">Save</button>
            </div>
        </div>
        <br/>
        <br/>
       
    </div>
    <?php
        $boundary = [];
        foreach ($location->boundary_arr as $key => $value) {
            $boundary [] = [
                            'lat'=>$value[1],
                            'lng'=>$value[0],
                            ];
        }
        // $coord = [];
        // if(count($location->complexities) > 0){
        //     foreach($location->complexities[0]['place_clues']['coordinates'] as $coordinates){
        //         $coord [] = [
        //                         'lat'=>$coordinates[1],
        //                         'lng'=>$coordinates[0],
        //                         ];
        //     }
        // }
        
    ?>
@endsection

@section('scripts')
    <!-- <script type="text/javascript" src="{{ asset('js/toastr.min.js') }}"></script> -->
    <script type="text/javascript">
        function initMap() {
            var uluru = { lat: {{ $location->latitude }} , lng: {{ $location->longitude }} };
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 18,
                center: uluru,
                mapTypeId: 'terrain'
            });

            
            var triangleCoords = [
                <?php echo json_encode($boundary) ?>
            ];

            
            // var marker = new google.maps.Marker({position: coord, map: map});
            var i=0;
            var icon = {
              url: "{{ asset('admin_assets/images/blue_marker.png') }}",
              // This marker is 20 pixels wide by 32 pixels high.
              scaledSize: new google.maps.Size(20, 32),
              // The origin for this image is (0, 0).
              //origin: new google.maps.Point(0, 0),
              // The anchor for this image is the base of the flagpole at (0, 32).
              //anchor: new google.maps.Point(10, 20)
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
                if(count($location->complexities) > 0){
                    $coord = [];
                        foreach($location->complexities[0]['place_clues']['coordinates'] as $coordinates){
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
                    for (var i =0; i < vertices.getLength(); i++) {
                        var xy = vertices.getAt(i);
                        /*boundary_arr[i] = xy.lng() +','+ xy.lat();*/
                        var arr = [];
                        arr.push(xy.lng());
                        arr.push(xy.lat());
                        coordinates.push(arr);
                    }
                    console.log(JSON.stringify(coordinates));
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
                google.maps.event.addListener(drawingManager, "overlaycomplete", function(event) {
                        var newShape = event.overlay;
                        newShape.type = event.type;
                    });
                google.maps.event.addListener(drawingManager, "overlaycomplete", function(event){
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
                    $('#latitude').val(JSON.stringify(coordinates));
                });
            <?php } ?>
            
            
            function overlayClickListener(overlay) {
                google.maps.event.addListener(overlay, "mouseup", function(event){
                    $('#boundary_arr').val(overlay.getPath().getArray());
                    console.log(overlay.getPath().getArray());
                    console.log(overlay.getPath());
                });
            }

        }
        $('#saveCoordinates').click(function(e){
          e.preventDefault();
          var formData = new FormData();
          formData.append("coordinates",$('#latitude').val());
          formData.append("place_id","{{$location->_id}}");
          formData.append("complexity",{{$complexity}});

          formData.append( "_token", $('meta[name="csrf-token"]').attr('content') );
          $.ajax({
                        type: "POST",
                        url: '{{ route("admin.storeStarComplexity") }}',
                        data: formData,
                        processData:false,
                        cache:false,
                        contentType: false,
                        success: function(response)
                        {
                            if (response.status == true) {
                                toastr.success(response.message);
                                // location.replace('{{route('admin.boundary_map',$location->_id)}}');
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