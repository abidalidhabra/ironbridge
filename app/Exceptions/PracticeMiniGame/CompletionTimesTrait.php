<?php
namespace App\Exceptions\PracticeMiniGame;

trait CompletionTimesTrait {

	private $completionTimes = 0;

    public function __construct($message, $completionTimes) 
    {
        $this->completionTimes = $completionTimes;
        parent::__construct($message);
    }
    
	public function getcompletionTimes()
    {
        return $this->completionTimes;
    }
}