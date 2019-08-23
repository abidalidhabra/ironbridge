<?php

namespace App\Refacing;

interface PriorToInsertRefaceable {

	public function prepareToInsert($miniGameData);
}