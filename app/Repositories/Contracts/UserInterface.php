<?php

namespace App\Repositories\Contracts;

use App\Models\v1\WidgetItem;

interface UserInterface {
	
	public function addGold(int $coins);

	public function deductGold(int $coins);
    
    public function deductSkeletonKeys(int $size);
    
    public function addWidgetItem(WidgetItem $WidgetItem);
    
    public function addWidgetItems(WidgetItem $WidgetItem);
    
    public function resetWidgets(WidgetItem $WidgetItem);
}