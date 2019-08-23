<?php

namespace App\Refacing;

interface Filterfable {
	
	/**
     * Filter-out the data from the given data.
     *
     * @param  array  $miniGameData
     *
     * @return array
     */
	public function filter($miniGameData);
}