<?php

namespace App\Services\Hunt;

use App\Repositories\User\UserRepository;
use App\Services\User\AddRelicService;
use stdClass;

class RelicService
{

	public $user;
	public $relic;
	public $dataToReturn;
	public $userRepository;

	public function __construct()
	{
        $dataToReturn = [ 'collected_piece'=> 0, 'collected_relic'=> new stdClass ];
	}

	public function relic()
	{
        $this->setRelic(
        	$this->userRepository->streamingRelic()
        );
	}

	public function addMapPiece()
    {
    	
    	$this->relic();

        if ($this->relic) {
            
            $collected = $this->collectedPieces();
            $totalPiecesRemaining = $this->relic->pieces - $collected;
            if ($totalPiecesRemaining <= 1) {
                $this->dataToReturn['collected_relic'] = $this->addRelic();
                $this->dataToReturn['streaming_relic'] = $this->userRepository->streamingRelic();
            }
            $this->dataToReturn['collected_piece'] = $this->addPiece();
        }
        return $this->dataToReturn;
    }

    public function addRelic()
    {
    	return (new AddRelicService)
    			->setUser($this->user)
    			->setRelicId($this->relic->id)
    			->add()
    			->getRelic(['_id', 'complexity','icon', 'number']);
    }

    public function addPiece()
    {
    	$this->user->user_relic_map_pieces()->create(['relic_id'=> $this->relic->id]);
    	return $this->collectedPieces();
    }

    public function collectedPieces()
    {
    	return $this->user->user_relic_map_pieces()->where(['relic_id'=> $this->relic->id])->count();
    }

    /**
     * @param mixed $relic
     *
     * @return self
     */
    public function setRelic($relic)
    {
        $this->relic = $relic;

        return $this;
    }

    /**
     * @param mixed $user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;
		$this->userRepository = new UserRepository($this->user);
        return $this;
    }
}