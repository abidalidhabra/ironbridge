<div class="newstitlebox_inputbox">
	<div class="form-group">
		<input type="text" name="email" value="{{$admin->email}}" placeholder="email">
	</div>
</div> 
<?php $module = ""; ?>
@foreach($permissions as $permission)
@if($module != $permission->module)<div class="clearfix"></div> <h5>{{$permission->module}}</h5> <hr><div class="clearfix"></div>@endif            
<div class="col-md-4 ml-auto">
	<div class="checkbox">
		<label><input type="checkbox" name="permissions[]" id="{{$permission->id}}" value="{{$permission->name}}" @if(in_array($permission->id,$assignedPermissions)) checked @endif>{{$permission->name}}</label>
	</div>
</div>
<?php $module = $permission->module; ?>             

@endforeach            

<label for="permissions[]" class="error"></label>
<input type="hidden" name="admin_id" id="admin_id" value="{{$admin->_id}}">