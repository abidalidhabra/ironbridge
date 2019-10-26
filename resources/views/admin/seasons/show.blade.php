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
                <a href="{{ route('admin.relics.create', $season->slug) }}" class="btn btn-success">Add Relic</a>
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
    <div class="customdatatable_box" id="addSeasonContainer">
        <div>
            <p>Season Slug: {{ $season->slug }} </p>
            <p>Status: {{ ($season->active)? 'Active': 'Inactive' }}</p>
            <div>
                <div class="img-container">
                    <p>Active Icon</p>
                    <img 
                        style="width: 80px;" 
                        src="{{ asset('storage/seasons/'.$season->id.'/'.$season->active_icon) }}" 
                        alt="season active icon">
                </div>
                <div class="img-container">
                    <p>Inactive Icon</p>
                    <img 
                        style="width: 80px;" 
                        src="{{ asset('storage/seasons/'.$season->id.'/'.$season->inactive_icon) }}" 
                        alt="season inactive icon">
                </div>
            </div>
        </div>
        <div>
            <h2>Relics</h2>
            @forelse($season->relics as $index=> $relic)
                <div>
                   <h4> Relic {{$index+1}} </h4> 
                   <div>
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
                   <p>Relic Name: {{ $relic->name }}</p>
                   <p>Relic Desc: {{ $relic->desc }}</p>
                   <p>Status: {{ ($relic->active)? 'Active': 'Inactive' }}</p>
                   <div>
                        <div class="img-container">
                            <p>Active Icon</p>
                            <img 
                                style="width: 80px;" 
                                src="{{ asset('storage/seasons/'.$season->id.'/'.$relic->active_icon) }}" 
                                alt="{{ $relic->name }} relic active icon">
                        </div>
                        <div class="img-container">
                            <p>Inactive Icon</p>
                            <img 
                                style="width: 80px;" 
                                src="{{ asset('storage/seasons/'.$season->id.'/'.$relic->inactive_icon) }}" 
                                alt="{{ $relic->name }} relic inactive icon">
                        </div>
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