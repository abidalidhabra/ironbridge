<?php

namespace App\Repositories\Contracts;

interface UserInterface {
	
	public function addGold(int $coins);

	public function deductGold(int $coins);
}