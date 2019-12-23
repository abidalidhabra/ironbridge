<?php

namespace App\Services\Hunt\LootDistribution;

use App\Repositories\User\UserRepository;
use App\Repositories\WidgetItemRepository;
use App\Services\Hunt\LootDistribution\LootTrait;

class WidgetDistribution
{
	use LootTrait;

	protected $widgetMagicNumber = 0;
	protected $widgets;
	protected $widget;
	protected $user;
	protected $widgetItem;

	public function setWidgetMagicNumber($magicNumber)
	{
		$this->widgetMagicNumber = $magicNumber;
		return $this;
	}

	public function getWidgetMagicNumber()
	{
		return $this->widgetMagicNumber;
	}

	public function setWidgets($widgets)
	{
		$this->widgets = collect($widgets);
		return $this;
	}

	public function getWidgets()
	{
		return $this->widgets;
	}

	public function setWidget($widget)
	{
		$this->widget = $widget;
		return $this;
	}

	public function getWidget()
	{
		return $this->widget;
	}

	public function setWidgetItem($widgetItem)
	{
		$this->widgetItem = $widgetItem;
		return $this;
	}

	public function getWidgetItem()
	{
		return $this->widgetItem;
	}

	public function spin()
	{

		$this->setWidgetMagicNumber(
			rand(1, 1000)
			// 210
		);

		return $this;
	}
	
	/**
	 * returns the WidgetItem model.
	 * @param  bool|boolean $recursive
	 * @return null
	 * @return WidgetItem Model
	 */
	public function open(bool $recursive = false)
	{

		if (!$recursive) {
			$this->setWidget(
				$this->widgets->where('min', '<=', $this->widgetMagicNumber)->where('max','>=',$this->widgetMagicNumber)->first()
			);
		}

		if (!$this->widget) {
			
			$this->skeletons(1);

		}else{

	      	if ($widgetItem = $this->findWidgetItem()) {
		      	
		      	$this->setWidgetItem(
		      		$widgetItem
		      	);
		      	
		      	$this->giveAwayToUser();

		      	$this->setResponse([
		      		'widget'=> $this->widgetItem
		      	]);
	      	}else{

	      		$this->getOtherWidget();
	      	}
		}
      	return $this;
	}

	/**
	 * returns the widgets of user's model.
	 * @return Collection
	 */
	public function getUserWidgets()
	{
		return collect($this->user->widgets);
	}

	/**
	 * return the added widget ids
	 * @return array
	 */
	public function giveAwayToUser()
	{
		if ($this->widgetItem->items) {
			return $this->userRepository->addWidgetItems($this->widgetItem);
		}else{
			return $this->userRepository->addWidgetItem($this->widgetItem);
		}
	}

	public function findWidgetItem()
	{
		$type  = $this->widget['type'];
		$name  = $this->widget['widget_name'];

        return (new WidgetItemRepository)->getModel()
    			->when($type != 'all', function ($query) use ($type){
		        	return $query->where('widget_category', $type);
		        })
		        ->havingGender($this->user->gender)
		        ->where('widget_name', $name)
		        ->whereNotIn('_id', $this->getUserWidgets()->pluck('id'))
		        ->select('_id', 'widget_name', 'avatar_id', 'widget_category')
		        ->first();
	}

	/**
	 * @return void
	 */
	public function getOtherWidget()
	{

		$type  = $this->widget['type'];
		$name  = $this->widget['widget_name'];
 
       	$this->setWidgets(
       		$this->widgets->reject(function($order) use ($name, $type) { 
       			return ($order['widget_name'] == $name && $order['type'] == $type); 
       		})
       	);

        $this->setWidget(
        	$this->widgets->where('min', '!=', 0)->where('max', '!=', 0)->sortByDesc('possibility')->first()
        );

        $this->open(true);
	}
}