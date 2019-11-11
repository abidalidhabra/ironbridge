<?php
namespace App\Exceptions\PracticeMiniGame;

trait CompletionTimesTrait {

	private $completionTimes = 0;
    private $availableSkeletonKeys = 0;
    private $availablePieces = 0;

    public function __construct($message, $completionTimes, $availableSkeletonKeys, $availablePieces) 
    {
        $this->completionTimes = $completionTimes;
        $this->availableSkeletonKeys = $availableSkeletonKeys;
        $this->availablePieces = $availablePieces;
        parent::__construct($message);
    }
    
	public function getcompletionTimes()
    {
        return $this->completionTimes;
    }

    public function getavailableSkeletonKeys()
    {
        return $this->availableSkeletonKeys;
    }

    public function getavailablePieces()
    {
        return $this->availablePieces;
    }
}