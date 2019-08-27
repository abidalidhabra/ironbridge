<?php

namespace App\Repositories\Contracts;

interface EventInterface {
	
	public function find($id, $columns);
}