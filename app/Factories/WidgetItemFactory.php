<?php

namespace App\Factories;

use App\Models\v1\WidgetItem;
use App\Repositories\Contracts\UserInterface;

class WidgetItemFactory
{
    private $userInterface;

    public function __construct($user)
    {
        $this->userInterface = app(UserInterface::class)($user);
    }

    public function initializeWidgetItem(WidgetItem $widgetItem){

        if ($widgetItem->items) {
            return $this->userInterface->addWidgetItems($widgetItem);
        }else{
            return $this->userInterface->addWidgetItem($widgetItem);
        }
    }

    public function resetWidgetItem(WidgetItem $widgetItem)
    {
        return $this->userInterface->resetWidgets($widgetItem);
    }
}