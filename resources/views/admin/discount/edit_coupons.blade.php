<div class="form-group">
    <label>Discount Code:</label>
    <input type="text" name="discount_code" class="form-control" placeholder="Enter Discount Code" value="{{ $discount->discount_code }}">
</div>
<div class="form-radio">
    <label>Discount Types:</label>
    <br/>
    <label class="radio-inline"><input type="radio" name="discount_types" value="gold_credit" @if($discount->discount_types=='gold_credit') {{ 'checked' }} @endif>Gold Credits</label>
    <label class="radio-inline"><input type="radio" name="discount_types" value="discount_percentage" @if($discount->discount_types=='discount_percentage') {{ 'checked' }} @endif>Discount Percentage</label>
    <label class="radio-inline"><input type="radio" name="discount_types" value="avatar_item" @if($discount->discount_types=='avatar_item') {{ 'checked' }} @endif>Avatar Item</label>

</div>
<div id="avatar_item_box" class="allavtitembox avatar_item_box" style="@if($discount->discount_types != 'avatar_item') {{ "display: none;" }} @endif">
    @forelse($widgetItem as $widget)
        @if(File::exists(public_path('admin_assets/widgets/'.$widget->id.'.png')))
        <div class="avt_itemboxset">
            <img class="card-img-top" src="{{ asset('admin_assets/widgets/'.$widget->id.'.png') }}">
            <div class="custboxslt">
                <label class="select_container">
                  <input type="checkbox" name="avatar_ids[]" value="{{ $widget->id }}" @if(in_array($widget->id,$discount->avatar_ids)) {{ 'checked' }} @endif>
                  <span class="checkmark"></span>
                </label>
            </div>
        </div>
        @endif
    @empty
    @endforelse
</div>
<input type="hidden" name="discount_id" value="{{ $discount->id }}" id="discount_id">
<br/>
<div class="form-group discount_box" style="@if($discount->discount_types == 'avatar_item') {{ "display: none;" }} @endif">
    @if($discount->discount_types=='gold_credit')
        <label>Gold Credits:</label>
        <input type="text" name="discount" class="form-control" placeholder="Enter Gold Credits" value="{{ $discount->discount }}">
    @elseif($discount->discount_types == 'discount_percentage')
        <label>Discount Percentage:</label>
        <input type="text" name="discount" class="form-control" placeholder="Enter Discount Percentage" value="{{ $discount->discount }}">
    @else
        <label>Gold Credits:</label>
        <input type="text" name="discount" class="form-control" placeholder="Enter Discount Percentage" value="{{ $discount->discount }}" disabled>
    @endif
</div>
<div class="form-group">
    <label>Number Of Uses:</label>
    <div class="row">
        <div class="col-md-6">
            <input type="text" name="number_of_uses" class="form-control" placeholder="Enter number of uses" value="{{ $discount->number_of_uses }}" @if($discount->number_of_uses == null) {{ 'disabled' }} @endif>
        </div>
        <div class="col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="number_of_uses_checked" @if($discount->number_of_uses == null) {{ 'checked' }} @endif>No Limit
            </label>
        </div>

</div>
<div class="form-radio mutitime_use" style="@if($discount->discount_types=='gold_credit' || $discount->discount_types=='avatar_item') {{ "display: none;" }} @endif" >
    <label>Single User Can Use Multiple Time?:</label>
    <br/>
    <label class="radio-inline"><input type="radio" name="can_mutitime_use" value="true" @if($discount->can_mutitime_use==true) {{ 'checked' }} @endif>Yes</label>
    <label class="radio-inline"><input type="radio" name="can_mutitime_use" value="false" @if($discount->can_mutitime_use==false) {{ 'checked' }} @endif>No</label>
</div>
<div class="form-group">
    <label>Expired In:</label>
    <div class="row">
        <div class="col-md-6">
            <input type="text" name="expiry_date" autocomplete="off" class="form-control" value="{{ ($discount->start_at)?$discount->start_at->format('M d, Y').' - '.$discount->end_at->format('M d, Y'):'' }}" placeholder="Enter number of uses" @if($discount->start_at == null) {{ 'disabled' }} @endif>
        </div>
        <div class="col-md-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="expiry_date_checked" @if($discount->start_at == null) {{ 'checked' }} @endif>No Limit
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <label>Description:</label>
    <textarea name="description" class="form-control">{{ $discount->description }}</textarea>
</div>
