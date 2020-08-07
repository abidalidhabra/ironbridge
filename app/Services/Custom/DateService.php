<?php

namespace App\Services\Custom;

use Carbon\Carbon;

class DateService
{
	
	public $date;

	public function __construct($date = null)
	{
		if (is_string($date)) {
        	$date = $this->__toCarbon($date);
		}
        $this->date = $date;
	}

	public function toCarbon()
	{
		$this->__toCarbon();
		return $this;
	}

	public function __toCarbon()
	{
		return $this->date = Carbon::parse($this->date);
	}

	public function dateTime($format = 'Y-m-d H:i:s')
	{
		return $this->date->format($format);
	}

    /**
     * @param mixed $date
     *
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }
}