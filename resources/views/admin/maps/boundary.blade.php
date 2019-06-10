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
    }
</style>
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="locationinfobox">
            <div class="inerdeta_locat">
                <h2 class="locatininfobtn"><span>Location Info</span> 
                    <a href="javascript:void(0);" class="btn btn-info btn-md" id="clearAllClues" data-action='delete'>Clear Clues</a>
                </h2>
                <h3><span>Place Name :</span> {{ $location->place_name }}</h3>
                <h3><span>City :</span> {{ $location->city }}</h3>
                <h3><span>Province :</span> {{ $location->province }}</h3>
                <h3><span>Country :</span> {{ $location->country }}</h3>
                <div class="locatininfoinerbtn">
                    <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>1]) }}" class="btn btn-info btn-md @if(in_array(1,$complexityarr)) active_btn @endif">
                    1 Star</a>
                </div>
                <div class="locatininfoinerbtn">
                    <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>2]) }}" class="btn btn-info btn-md @if(in_array(2,$complexityarr)) active_btn @endif" >2 Stars</a>
                </div>
                <div class="locatininfoinerbtn">
                    <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>3]) }}" class="btn btn-info btn-md @if(in_array(3,$complexityarr)) active_btn @endif">3 Stars</a>
                </div>
                <div class="locatininfoinerbtn">
                    <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>4]) }}" class="btn btn-info btn-md @if(in_array(4,$complexityarr)) active_btn @endif">4 Stars</a>
                </div>
                <div class="locatininfoinerbtn">
                    <a href="{{ route('admin.starComplexityMap',['id'=>$location->_id,'complexity'=>5]) }}" class="btn btn-info btn-md @if(in_array(5,$complexityarr)) active_btn @endif">5 Stars</a>
                </div>
            </div>
             <div class="customdatatable_box">
                <div id="map"></div>
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
        
    ?>
@endsection

@section('scripts')
    <!-- <script type="text/javascript" src="{{ asset('js/toastr.min.js') }}"></script> -->
    <script type="text/javascript">
$(document).on('click','#clearAllClues',function(e){
  e.preventDefault();
  // alert();
  //DELETE ACCOUNT
                $(this).confirmation({
                    container:"body",
                    btnOkClass:"btn btn-sm btn-success",
                    btnCancelClass:"btn btn-sm btn-danger",
                    onConfirm:function(event, element) {
                        var id = element.attr('data-id');
                        $.ajax({
                            type: "delete",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '{{ route("admin.clearAllClues",$location->_id) }}',
                            data: {id : id},
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
                    }
                });  
});


        function initMap() {
            var uluru = { lat: {{ $location->latitude }} , lng: {{ $location->longitude }} };
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 16,
                center: uluru,
                styles:[
                          {
                            "elementType": "geometry",
                            "stylers": [
                              {
                                "color": "#ebe3cd"
                              }
                            ]
                          },
                          {
                            "elementType": "labels",
                            "stylers": [
                              {
                                "visibility": "off"
                              }
                            ]
                          },
                          {
                            "elementType": "labels.text.fill",
                            "stylers": [
                              {
                                "color": "#523735"
                              }
                            ]
                          },
                          {
                            "elementType": "labels.text.stroke",
                            "stylers": [
                              {
                                "color": "#f5f1e6"
                              }
                            ]
                          },
                          {
                            "featureType": "administrative",
                            "elementType": "geometry.stroke",
                            "stylers": [
                              {
                                "color": "#c9b2a6"
                              }
                            ]
                          },
                          {
                            "featureType": "administrative.land_parcel",
                            "stylers": [
                              {
                                "visibility": "off"
                              }
                            ]
                          },
                          {
                            "featureType": "administrative.land_parcel",
                            "elementType": "geometry.stroke",
                            "stylers": [
                              {
                                "color": "#dcd2be"
                              }
                            ]
                          },
                          {
                            "featureType": "administrative.land_parcel",
                            "elementType": "labels.text.fill",
                            "stylers": [
                              {
                                "color": "#ae9e90"
                              }
                            ]
                          },
                          {
                            "featureType": "administrative.neighborhood",
                            "stylers": [
                              {
                                "visibility": "off"
                              }
                            ]
                          },
                          {
                            "featureType": "landscape.natural",
                            "elementType": "geometry",
                            "stylers": [
                              {
                                "color": "#dfd2ae"
                              }
                            ]
                          },
                          {
                            "featureType": "poi",
                            "elementType": "geometry",
                            "stylers": [
                              {
                                "color": "#dfd2ae"
                              }
                            ]
                          },
                          {
                            "featureType": "poi",
                            "elementType": "labels.text.fill",
                            "stylers": [
                              {
                                "color": "#93817c"
                              }
                            ]
                          },
                          {
                            "featureType": "poi.park",
                            "elementType": "geometry.fill",
                            "stylers": [
                              {
                                "color": "#a5b076"
                              }
                            ]
                          },
                          {
                            "featureType": "poi.park",
                            "elementType": "labels.text.fill",
                            "stylers": [
                              {
                                "color": "#447530"
                              }
                            ]
                          },
                          {
                            "featureType": "road",
                            "elementType": "geometry",
                            "stylers": [
                              {
                                "color": "#f5f1e6"
                              }
                            ]
                          },
                          {
                            "featureType": "road.arterial",
                            "elementType": "geometry",
                            "stylers": [
                              {
                                "color": "#fdfcf8"
                              }
                            ]
                          },
                          {
                            "featureType": "road.highway",
                            "elementType": "geometry",
                            "stylers": [
                              {
                                "color": "#f8c967"
                              }
                            ]
                          },
                          {
                            "featureType": "road.highway",
                            "elementType": "geometry.stroke",
                            "stylers": [
                              {
                                "color": "#e9bc62"
                              }
                            ]
                          },
                          {
                            "featureType": "road.highway.controlled_access",
                            "elementType": "geometry",
                            "stylers": [
                              {
                                "color": "#e98d58"
                              }
                            ]
                          },
                          {
                            "featureType": "road.highway.controlled_access",
                            "elementType": "geometry.stroke",
                            "stylers": [
                              {
                                "color": "#db8555"
                              }
                            ]
                          },
                          {
                            "featureType": "road.local",
                            "elementType": "labels.text.fill",
                            "stylers": [
                              {
                                "color": "#806b63"
                              }
                            ]
                          },
                          {
                            "featureType": "transit.line",
                            "elementType": "geometry",
                            "stylers": [
                              {
                                "color": "#dfd2ae"
                              }
                            ]
                          },
                          {
                            "featureType": "transit.line",
                            "elementType": "labels.text.fill",
                            "stylers": [
                              {
                                "color": "#8f7d77"
                              }
                            ]
                          },
                          {
                            "featureType": "transit.line",
                            "elementType": "labels.text.stroke",
                            "stylers": [
                              {
                                "color": "#ebe3cd"
                              }
                            ]
                          },
                          {
                            "featureType": "transit.station",
                            "elementType": "geometry",
                            "stylers": [
                              {
                                "color": "#dfd2ae"
                              }
                            ]
                          },
                          {
                            "featureType": "water",
                            "elementType": "geometry.fill",
                            "stylers": [
                              {
                                "color": "#b9d3c2"
                              }
                            ]
                          },
                          {
                            "featureType": "water",
                            "elementType": "labels.text.fill",
                            "stylers": [
                              {
                                "color": "#92998d"
                              }
                            ]
                          }
                        ],
                mapTypeId: 'terrain'
            });

            
            var triangleCoords = [
                <?php echo json_encode($boundary) ?>
            ];

            var marker = new google.maps.Marker({position: uluru, map: map});

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
        }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0AzhRBk1LARqw9SDz9qwpAkTYDaQNe6o&callback=initMap">
    </script>
@endsection