<?php

namespace App\Refacing;

interface MarkEventMGAsCompleteInterface {

	public function prepareToInsert($miniGameData);

	public function output($miniGameData);
}