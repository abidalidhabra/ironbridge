@section('title','Ironbridge1779 | Season Detail')
@extends('admin.layouts.admin-app')

@section('style')

@endsection

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="">               
            <div class="col-md-12 text-right">
                <a href="{{ route('admin.seasons.index') }}" class="btn back-btn">Back</a>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <h3>{{ $season->name }} Details: </h3>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box allboxinercoversgm" id="addSeasonContainer">
        <div class="seastaicoboxsettop">
            <h2>Season Detail:</h2>
            <p>Slug: <span>{{ $season->slug }}</span> </p>
            <p>Status: <span>{{ ($season->active)? 'Active': 'Inactive' }}</span></p>
            <div class="">
                <div class="img-container imgiconboxsetiner">
                    <p>Icon</p>
                    <a data-fancybox="{{ $season->id }}" href="{{ $season->icon }}">
                        <img src="{{ $season->icon }}" alt="season icon">
                    </a>
                </div>
               {{--  <div class="img-container imgiconboxsetiner">
                    <p>Inactive Icon</p>
                    <img 
                        
                        src="{{ $season->icon }}" 
                        alt="season inactive icon">
                </div> --}}
            </div>
        </div>
        <div class="relisetinerript">
            <h2>Relics</h2>
            @forelse($season->relics as $index=> $relic)
                <div class="allreicontitcover">
                    <div class="titleedtdeltbtn">
                        <h4> Relic <span>{{$index+1}}</span> </h4> 
                        <div class="editdeltboxbtn">
                            @if(auth()->user()->hasPermissionTo('Edit Seasonal Hunt'))
                                <a href="{{ route('admin.relics.edit', $relic->id) }}" data-toggle="tooltip" title="Edit" >
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endif

                            @if(auth()->user()->hasPermissionTo('Delete Seasonal Hunt'))
                                <a href="{{ route('admin.relics.destroy', $relic->id) }}" data-toggle="tooltip" data-action="delete" title="Edit" >
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endif
                        </div> 
                    </div>                  
                   <p>Relic Name: <span>{{ $relic->name }}</span></p>
                   <p>Relic Desc: <span>{{ $relic->desc }}</span></p>
                   <p>Status: <span>{{ ($relic->active)? 'Active': 'Inactive' }}</span></p>
                   <div>
                        <div class="img-container imgiconboxsetiner">
                            <p>Icon</p>
                            <a data-fancybox="{{ $season->name }}" href="{{ $relic->icon }}">
                                <img style="width: 80px;" src="{{ $relic->icon }}" alt="{{ $relic->name }} relic icon">
                            </a>
                        </div>
                        {{-- <div class="img-container imgiconboxsetiner">
                            <p>Inactive Icon</p>
                            <img 
                                style="width: 80px;" 
                                src="{{ $relic->icon }}" 
                                alt="{{ $relic->name }} relic inactive icon">
                        </div> --}}
                    </div>
                </div>
            @empty
                <h4>No Relics yet present in this season.</h4>
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