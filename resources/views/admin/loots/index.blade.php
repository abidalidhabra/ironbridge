@section('title','Ironbridge1779 | Relics')
@extends('admin.layouts.admin-app')

@section('content')
<div class="right_paddingboxpart">
    <div class="users_datatablebox">
        <div class="row">
            <div class="col-md-6">
                <h3>Loots</h3>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <div class="customdatatable_box">
        <table class="table table-striped table-hover datatables" style="width: 100%;" id="dataTable">
            <thead>
                <tr>
                    <th>Sr.</th>
                    <th>Number</th>
                    <th>TH Complexity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loots as $key => $value)
                    <tr>
                      <th scope="row">1</th>
                      <td>{{ $key }}</td>
                      <td>{{ implode(',', array_keys($value->groupBy('complexity')->toArray()))}}</td>
                      <td><a href="{{ route('admin.loots.show',$key) }}" data-action="View" data-toggle="tooltip" title="View" ><i class="fa fa-eye iconsetaddbox"></i></a></td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#dataTable').DataTable();
</script>
@endsection