<div class="form-group">
    <label>Discount Code:</label>
    <input type="text" name="discount_code" class="form-control" placeholder="Enter Discount Code" value="{{ $discount->discount_code }}">
</div>
<div class="form-radio">
    <label>Discount Types:</label>
    <br/>
    <label class="radio-inline"><input type="radio" name="discount_types" value="gold_credit" @if($discount->discount_types=='gold_credit') {{ 'checked' }} @endif>Gold Credits</label>
    <label class="radio-inline"><input type="radio" name="discount_types" value="discount_percentage" @if($discount->discount_types=='discount_percentage') {{ 'checked' }} @endif>Discount Percentage</label>
</div>
<input type="hidden" name="discount_id" value="{{ $discount->id }}" id="discount_id">
<div class="form-group">
    <label>Discount:</label>
    <input type="text" name="discount" class="form-control" placeholder="Enter Discount" value="{{ $discount->discount }}">
</div>
<div class="form-group">
    <label>Number Of Uses:</label>
    <input type="text" name="number_of_uses" class="form-control" placeholder="Enter number of uses" value="{{ $discount->number_of_uses }}">
</div>
<div class="form-radio mutitime_use" style="@if($discount->discount_types=='gold_credit') {{ "display: none;" }} @endif" >
    <label>Single User Can Use Multiple Time?:</label>
    <br/>
    <label class="radio-inline"><input type="radio" name="can_mutitime_use" value="true" @if($discount->can_mutitime_use==true) {{ 'checked' }} @endif>Yes</label>
    <label class="radio-inline"><input type="radio" name="can_mutitime_use" value="false" @if($discount->can_mutitime_use==false) {{ 'checked' }} @endif>No</label>
</div>
<div class="form-group">
    <label>Expired In:</label>
    <input type="text" name="expiry_date" class="form-control" value="{{ $discount->start_at->format('M d, Y').' - '.$discount->end_at->format('M d, Y') }}" placeholder="Enter number of uses">
</div>
<div class="form-group">
    <label>Description:</label>
    <textarea name="description" class="form-control">{{ $discount->description }}</textarea>
</div>
