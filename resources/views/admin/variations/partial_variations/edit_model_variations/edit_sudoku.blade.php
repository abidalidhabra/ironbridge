<div class="form-group col-md-6">
    <label class="form-label">Variation size</label>
    <input type="text"  name="variationSize" value="{{ $variations->variation_size }}" id="variationSize" class="form-control">
</div>
<div class="form-group col-md-6">
    <label class="form-label">Sudoku Id</label>
    <select name="sudoku_id" class="form-control">
        <option <?php if ($variations->sudoku_id == 1) { echo "selected";} ?> value="1">1</option>
        <option <?php if ($variations->sudoku_id == 2) { echo "selected";} ?> value="2">2</option>
        <option <?php if ($variations->sudoku_id == 3) { echo "selected";} ?> value="3">3</option>
        <option <?php if ($variations->sudoku_id == 4) { echo "selected";} ?> value="4">4</option>
        <option <?php if ($variations->sudoku_id == 5) { echo "selected";} ?> value="5">5</option>
        <option <?php if ($variations->sudoku_id == 6) { echo "selected";} ?> value="6">6</option>
        <option <?php if ($variations->sudoku_id == 7) { echo "selected";} ?> value="7">7</option>
        <option <?php if ($variations->sudoku_id == 8) { echo "selected";} ?> value="8">8</option>
        <option <?php if ($variations->sudoku_id == 9) { echo "selected";} ?> value="9">9</option>
        <option <?php if ($variations->sudoku_id == 10) { echo "selected";} ?> value="10">10</option>
        <option <?php if ($variations->sudoku_id == 11) { echo "selected";} ?> value="11">11</option>
        <option <?php if ($variations->sudoku_id == 12) { echo "selected";} ?> value="12">12</option>
        <option <?php if ($variations->sudoku_id == 13) { echo "selected";} ?> value="13">13</option>
        <option <?php if ($variations->sudoku_id == 14) { echo "selected";} ?> value="14">14</option>
        <option <?php if ($variations->sudoku_id == 15) { echo "selected";} ?> value="15">15</option>
        <option <?php if ($variations->sudoku_id == 16) { echo "selected";} ?> value="16">16</option>
    </select>
</div>
<input type="hidden" name="row" value="0">
<input type="hidden" name="column" value="0">
<input type="hidden" name="number_generate" value="0">
<input type="hidden"  name="target" value="0">
<input type="hidden" name="no_of_balls" value="0">
<input type="hidden" name="bubble_level_id" value="0">