<div class="newstitlebox_inputbox">
	<div class="form-group">
		<input type="text" name="email" value="{{$admin->email}}" placeholder="email">
	</div>
</div> 
@foreach($permissions as $key => $permissionData)
                                <?php $module=""; ?>             
                                        
<div class="alltitandchebox">
    <div class="userdeta_titlebox">
        <h5>{{ $key }}</h5>
    </div>
    <div class="checkuserditbox">
        @forelse($permissionData  as $permission)
        <div class="colmd4box">
            <div class="checkbox">
                <label><input type="checkbox" name="permissions[]" id="{{$permission->id}}" value="{{$permission->name}}" @if(in_array($permission->id,$assignedPermissions)) checked @endif>{{$permission->name}}</label>
            </div>
        </div>
        <?php $module = $permission->module; ?>             
        @empty
        @endforelse
    </div>
</div>

@endforeach

         

<label for="permissions[]" class="error"></label>
<input type="hidden" name="admin_id" id="admin_id" value="{{$admin->_id}}">