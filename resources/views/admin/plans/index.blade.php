@section('title','Ironbridge1779 | Plans')
@extends('admin.layouts.admin-app')
@section('styles')

@endsection
@section('content')
<div class="right_paddingboxpart">
    <!-- <div class="backbtn">
        <a href="{{ route('admin.userList') }}">Back</a>
    </div> -->
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Plans List</h3>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <!-- <div class="customdatatable_box"> -->
    <div class="verified_detlisbox">
        <ul>
            <h3 class="text-center">Plans</h3>
            @forelse($plans as $plan)
            <div class="col-md-6">
                <li>
                    <img src="{{ asset('admin_assets/svg/news.svg') }}">
                    <a href="javascript:void(0)"  data-id="{{ $plan->id }}" data-action="edit" >
                        <h3>{{ $plan->name }}</h3>
                        <p>{{ ucfirst(str_replace('_',' ',$plan->type)) }}</p>
                    </a>
                    <!-- <a href="javascript::void(0);" data-id="{{ $plan->id }}" data-action="edit" class="btn btn-primary edit_plans">Edit</a> -->
                </li>
            </div>
            @empty
                <div class="col-md-6">
                    <li>
                        <img src="{{ asset('admin_assets/svg/news.svg') }}">
                        <a href="javascript:void(0)">
                            <h3>Data not found</h3>
                        </a>
                    </li>
                </div>
            @endforelse
        </ul>
    </div>
</div>
<!-- EDIT PLANS -->
<div class="modal fade" id="editModel" role="dialog">

</div>
<!-- END EDIT PLANS -->
@endsection

@section('scripts')
    <script type="text/javascript">
    $(document).ready(function() {
        //GET USER LIST
         /* EDIT MODAL OPEN */
        $(document).on('click','[data-action="edit"]',function(){
            var id = $(this).attr("data-id");
            // var id = $(this).data('id');
            var url ='{{ route("admin.plans.edit",':id') }}';
            url = url.replace(':id',id);
            $.ajax({
                type: "GET",
                url: url,
                // data: {id : id},
                success: function(response)
                {
                    if (response.status == true) {
                        // toastr.success(response.message);
                        $('#editModel').html(response.html);
                        $('#editModel').modal('show');
                    } else {
                        toastr.warning(response.message);
                    }
                }
            });
        })
        /* END EDIT MODEL OPEN */

        /* UPDATE FORM */
        $(document).on('submit','#editPlansForm',function(e){
            e.preventDefault();
            var id = $('#plan_id').val();
            var formData = new FormData($('#editPlansForm')[0]);
            $.ajax({
                type: "POST",
                url: '{{ route("admin.plans.update","") }}/'+id,
                data: formData,
                processData:false,
                cache:false,
                contentType: false,
                success: function(response)
                {
                    if (response.status == true) {
                        toastr.success(response.message);
                        location.reload(true);
                        $('#editModel').modal('hide');
                    } else {
                        toastr.warning(response.message);
                    }
                }
            })
        });
        /* END UPDATE FORM */
    });


    </script>
@endsection