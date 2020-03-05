<?php

namespace App\Services;

use App\Models\v3\AssetsLog;
use App\Services\Event\EventUserService;
use App\Services\Traits\UserTraits;

class AssetsLogService
{
	use UserTraits;

	protected $model;
	protected $type;
	protected $from;

	public function __construct($type, $from)
	{
		$this->model = new AssetsLog;
		$this->model->type = $type;
        $this->model->from = $from;
	}

	public function compasses($size)
	{
		if ($event = (new EventUserService)->setUser($this->user)->running()) {
			$this->model->compasses = (int)$size;
			$this->model->event_id = $event->id;
			return $this;
        }else{
            throw new Exception("No event running in your home-town or you are not participated in any of running events.");
        }
	}

	public function save()
	{
		$this->model->user_id = $this->user->id;
		$this->model->save();
	}
}