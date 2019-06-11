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
   
    .locatininfobtn a{
      background: #C0B08C;
      border: 0px;
      font-size: 15px;
      font-family: 'Montserrat-Regular';
      display: inline-block;
      float: right;
    }
    .locatininfobtn{
      display: inline-block;
      width: 100%;
      padding-right: 18px;
    }
    .locatininfobtn span{
      display: inline-block;
      float: left;
      padding-top: 3px;
    }

    .locatininfoinerbtn{
      margin: 5px;
      display: inline-block;
      margin-right: 1px;
      margin-left: 2px;
    }
    .locatininfoinerbtn a{      
      font-size: 15px;
      font-family: 'Montserrat-Regular';
      border: 1px solid #C0B08C;
      color: #000;
      background: transparent ;
      border: 1px solid #C0B08C;
    }
    .locatininfoinerbtn a.active_btn{
      background: #C0B08C;
      border: 0px;
      color: #fff;
    }
   
    .locatininfoinerbtn a:hover{
      background: #C0B08C;
      border: 1px solid #C0B08C;
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
                        <a href="javascript:void(0);" class="btn btn-info btn-md" data-action="remove_stars" data-id='{{ $id }}' data-complexity='{{ $complexity }}'>Clear Stars</a>
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
                    <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>1]) }}" class="btn btn-info btn-md @if($complexity == 1) active_btn @endif">
                    1 Star</a>
                </div>
                <div class="locatininfoinerbtn">
                    <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>2]) }}" class="btn btn-info btn-md @if($complexity == 2) active_btn @endif" >2 Stars</a>
                </div>
                <div class="locatininfoinerbtn">
                    <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>3]) }}" class="btn btn-info btn-md @if($complexity == 3) active_btn @endif">3 Stars</a>
                </div>
                <div class="locatininfoinerbtn">
                    <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>4]) }}" class="btn btn-info btn-md @if($complexity == 4) active_btn @endif">4 Stars</a>
                </div>
                <div class="locatininfoinerbtn">
                    <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>5]) }}" class="btn btn-info btn-md @if($complexity == 5) active_btn @endif">5 Stars</a>
                </div>
                <input type="hidden" name="coordinates[]" id="latitude">
            </div>
             <div class="customdatatable_box">
                <div id="map"></div>
            </div>
            @if(count($location->complexities) == 0)
            <div class="pull-right modal-footer">
                    <button type="button" class="btn btn-success" id="saveCoordinates">Save</button>
            </div>
            @endif
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

            //var marker = new google.maps.Marker({position: uluru, map: map});
            var i=0;
            var icon = {
              url: "{{ asset('admin_assets/images/blue_marker.png') }}",
              // This marker is 20 pixels wide by 32 pixels high.
              scaledSize: new google.maps.Size(20, 32),
              // The origin for this image is (0, 0).
              origin: new google.maps.Point(0, 0),
              // The anchor for this image is the base of the flagpole at (0, 32).
              anchor: new google.maps.Point(0, 32)
            };
          <?php  if(count($location->complexities) > 0){ 
            foreach($location->complexities[0]['place_clues']['coordinates'] as $coordinates){
            ?>
                  var coord = { lat: {{ $coordinates[0] }} , lng: {{ $coordinates[1] }} };
                  new google.maps.Marker({
                      position: coord,
                      map: map,
                      size:[10,10],
                      icon:icon
                  });
                  i++;
          <?php } } ?>

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
            var goldStar = {
                              path: 'M 125,5 155,90 245,90 175,145 200,230 125,180 50,230 75,145 5,90 95,90 z',
                              fillColor: 'yellow',
                              fillOpacity: 0.8,
                              scale: 1,
                              strokeColor: 'gold',
                              strokeWeight: 14
                            };
            var i=0;
            var coordinates = [];
            var getMarkerUniqueId= function(lat, lng) {
                return lat + '_' + lng;
            }
            var marker = [];
            var remove_item = [];
            bermudaTriangle.addListener('click', function (event) {
                console.log(coordinates.length);
                if(coordinates.length < 5){
                    var markerId = getMarkerUniqueId(event.latLng.lat().toFixed(6) , event.latLng.lng().toFixed(6));
                    
                    var marker = new google.maps.Marker({position: event.latLng,
                            map:map,
                            icon:icon,
                            id: 'marker_' + markerId
                        });
                    google.maps.event.addListener(marker, "click", function (point) {
                        var markerId = getMarkerUniqueId(point.latLng.lat(), point.latLng.lng());
                        // remove_item[];
                        removeMarker(marker, markerId,remove_item); // remove it
                    });
                    
                    $('#coordinates').append(event.latLng.lat().toFixed(6)+" "+", "+event.latLng.lng().toFixed(6)+'<br>');

                        var arr = [];
                        arr.push(event.latLng.lat());
                        arr.push(event.latLng.lng());
                        coordinates[i]= arr;
                        $('#latitude').val(JSON.stringify(coordinates));
                    i++;
                } 
            });

            var removeMarker = function(marker, markerId) {
                marker.setMap(null); // set markers setMap to null to remove it from map
                delete marker[markerId]; // delete marker instance from markers object
            };
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
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0AzhRBk1LARqw9SDz9qwpAkTYDaQNe6o&callback=initMap">
    </script>
@endsection