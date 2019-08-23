<?php

namespace App\Refacing;

interface Refaceable {
	
	/**
     * Reface the data to for response client.
     *
     * @param  array  $miniGameData
     *
     * @return array
     */
	public function output(array $miniGameData);
}