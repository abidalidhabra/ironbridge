@section('title','Ironbridge1779 | Season Detail')
@extends('admin.layouts.admin-app')

@section('style')

@endsection

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="">               
            <div class="col-md-12 text-right">
                <a href="{{ route('admin.relics.index') }}" class="btn back-btn">Back</a>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <h3>Relics Details: </h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box allboxinercoversgm" id="addSeasonContainer">
        <div class="seastaicoboxsettop">
            <h2>Relics Detail:</h2>
            <p>TH Complexity: <span>{{ $relic->complexity }}</span> </p>
            <div class="">
                <div class="img-container imgiconboxsetiner">
                    <p>Relic Image</p>
                    <a data-fancybox="{{ $relic->id }}" href="{{ $relic->icon }}">
                        <img src="{{ $relic->icon }}" alt="season icon">
                    </a>
                </div>
               
            </div>
        </div>
        <div class="relisetinerript">
            <h2>Relic Map Piece</h2>
            @forelse($relic->pieces as $index=> $piece)
                <div class="allreicontitcover">
                    <div class="titleedtdeltbtn">
                        <h4>Relic Map Piece {{$index+1}}</h4> 
                    </div>                  
                   <div>
                        <div class="img-container imgiconboxsetiner">
                            <p>Map Image</p>
                            <a data-fancybox="piece image" href="{{ $piece['image'] }}">
                                <img style="width: 80px;" src="{{ $piece['image'] }}" alt="piece image">
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <h4>No Data Found</h4>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    $(document).on('submit', '#addSeasonForm', function(e) {
        e.preventDefault();
        if(validate()) {
            $.ajax({
                type: "POST",
                url: '{{ route('admin.seasons.store') }}',
                data: new FormData(this),
                contentType: false,
                processData: false,
                cache: false,
                success: function(response)
                {
                    if (response.status == true) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = '{{ route('admin.sponser-hunts.index') }}';
                        }, 2000)
                    } else {
                        toastr.warning('You are not authorized to access this page.');
                    }
                },
                error: function(xhr, exception) {
                    let error = JSON.parse(xhr.responseText);
                    toastr.error(error.message);
                }
            });
        }
    });

    // function initializeDeletePopup() {
        $("a[data-action='delete']").confirmation({
            container:"body",
            btnOkClass:"btn btn-sm btn-success",
            btnCancelClass:"btn btn-sm btn-danger",
            onConfirm:function(event, element) {
                event.preventDefault();
                $.ajax({
                    type: "DELETE",
                    url: element.attr('href'),
                    success: function(response){
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
    // }
</script>
@endsection