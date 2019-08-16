@section('title','Ironbridge1779 | GAME VARIATION')
@extends('admin.layouts.admin-app')
@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css">
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
                <h3>Event</h3>
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
            <input type="hidden" name="">
            <!-- HUNT CLUE START CODE -->
            <div class="daingaemtitlebox">
                <h4>Hunt Details</h4>
            </div>
            <div class="allbasicdirmain">                
                <div class="allbasicdirbox">
                    <input type="hidden" name="id" value="{{ $id }}"> 
                    <div class="form-group col-md-4">
                        <label class="form-label">Map Reveal Date</label>
                        <input type="text" name="map_reveal_date" class="form-control datetimepicker" placeholder="Enter the map reveal date">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">Search Place Name</label>
                        <select class="form-control hunts" name="search_place_name" id="hunts">
                            <option value="">Select Place</option>
                            @forelse($hunts as $hunt)
                                <option value="{{ $hunt->id }}">{{ $hunt->name }}</option>
                            @empty
                            @endforelse
                        </select>
                        <!-- <input type="text" name="search_place_name" id="search_place_name" class="form-control" placeholder="Enter the Search Place Name" autocomplete="off"> -->
                    </div>
                </div>
            </div>
            <!-- END HUNT CLUE  -->

            <div class="form-group col-md-12">
                <!-- <a href="{{ url('admin/miniGame',$id) }}" class="btn btn-success btnSubmit">PREVIOUS</a> -->

                <button type="submit" class="btn btn-success btnSubmit">SUBMIT</button>
            </div>
    </form>
    </div>
</div>


@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0AzhRBk1LARqw9SDz9qwpAkTYDaQNe6o&libraries=places,drawing"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
    <script type="text/javascript">
        /* DATE TIME PICKER */
        $('.datetimepicker').datetimepicker();

        /* SUBMIT FORM */
        $(document).ready(function() {
     
            $(document).on('submit','#addEventForm',function(e){
                e.preventDefault();
                formData = new FormData($(this)[0]);
                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.event.addHuntDetails") }}',
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){},
                    success:function(response) {
                        if (response.status == true) {
                            toastr.success(response.message);
                            window.location.href = '{{ route("admin.event.index")}}';
                        } else {
                            toastr.warning(response.message);
                        }
                    },
                    complete:function(){},
                    error:function(){}
                });
            })

            $('.hunts').select2();
        });
    </script>
@endsection