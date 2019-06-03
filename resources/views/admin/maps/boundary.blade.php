@section('title','Ironbridge1779 | Maps')
@extends('admin.layouts.admin-app')
@section('styles')
<style>
    #map {
        height: 500px;
        width: 100%;
    }
</style>
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.min.css') }}"> -->
@endsection
@section('content')
    <div class="right_paddingboxpart">
        <div class="users_datatablebox">
            <h3><span>Place Name :</span> {{ $location->place_name }}</h3>
            <h3><span>City :</span> {{ $location->city }}</h3>
            <h3><span>Province :</span> {{ $location->province }}</h3>
            <h3><span>Country :</span> {{ $location->country }}</h3>
        </div>
        <br/>
        <br/>
        <div class="customdatatable_box">
            <div id="map"></div>
        </div>
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
                zoom: 16,
                center: uluru,
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