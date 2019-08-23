<?php

namespace App\Refacing;

interface PriorToInsertRefaceable {
	
	/**
     * Reface the data prior to database insertion.
     *
     * @param  array  $miniGameData
     *
     * @return array
     */
	public function prepareToInsert(array $miniGameData);
}