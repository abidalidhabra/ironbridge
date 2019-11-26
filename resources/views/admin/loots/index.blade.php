@section('title','Ironbridge1779 | Loots')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Loots</h3>
            </div>
            <div class="col-md-6 text-right modalbuttonadd">
                <a href="{{ route('admin.loots.create') }}" class="btn btn-info btn-md">Add</a>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box col-md-12">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th>Sr.</th>
                    <th>Loot Table Number</th>
                    <th>Relics</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 1;
                @endphp
                @forelse($loots as $key => $value)
                    <tr>
                        <th scope="row">{{ $i }}</th>
                        <td>{{ $key }}</td>
                        <td>
                            @if($value[0]->status == true)
                                <button data-action="status" data-status="false" data-id="{{ $key }}" class="btn btn-success btn-xs">Active</button>
                            @else
                                <button data-action="status" data-status="true" data-id="{{ $key }}" class="btn btn-danger btn-xs">InActive</button>
                            @endif
                        </td>
                        <td>
                            @if(count($value[0]->relics_info) > 0)
                                @php
                                    echo implode(',',$value[0]->relics_info->pluck('name')->toArray());
                                @endphp
                            @else
                                -
                            @endif    
                        </td>
                        <td>
                            <a href="javascript:void(0)" data-id="{{ $key }}" data-action="delete" data-toggle="tooltip" title="Delete"  data-toggle="confirmation"><i class="fa fa-trash iconsetaddbox"></i></a>
                            <a href="{{ route('admin.loots.show',$key) }}" data-action="View" data-toggle="tooltip" title="View" ><i class="fa fa-eye iconsetaddbox"></i></a>
                        </td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
   
    // $("table").delegate("button[data-action='status']", "click", function() {
    $(document).on('click',"button[data-action='status']",function(){
        var status = $(this).data('status');
        var id = $(this).data('id');
        $.ajax({
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route("admin.loots.changeStatus") }}',
            data: {id : id ,status:status},
            success: function(response)
            {
                if (response.status == true) {
                    location.reload(true);
                    toastr.success(response.message);
                } else {
                    toastr.warning(response.message);
                }
            },error: function (xhr) {
                let error = JSON.parse(xhr.responseText);
                toastr.error(error.message);
            }
        });
    })

    // $(document).on('click',"a[data-action='delete']",function(){
     $('#dataTable').DataTable(
    $("a[data-action='delete']").confirmation({
        container:"body",
        btnOkClass:"btn btn-sm btn-success",
        btnCancelClass:"btn btn-sm btn-danger",
        onConfirm:function(event, element) {
            var id = element.attr('data-id');
            $.ajax({
                type: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route("admin.loots.destroy","/") }}/'+id,
                data: {id : id},
                success: function(response)
                {
                    if (response.status == true) {
                        location.reload(true);
                        toastr.success(response.message);
                    } else {
                        toastr.warning(response.message);
                    }
                },error: function (xhr) {
                    let error = JSON.parse(xhr.responseText);
                    toastr.error(error.message);
                }
            });
        }
    })
    );

</script>
@endsection