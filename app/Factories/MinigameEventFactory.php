<?php

namespace App\Factories;

use App\Events\MiniGame\Completed;
use App\Events\MiniGame\Exited;
use App\Events\MiniGame\Failed;
use App\Models\v2\MinigameHistory;
use Exception;

class MinigameEventFactory
{

    private $type;
    private $actionType;
    private $rawData;
    private $data;

    public function __construct(string $type, string $actionType, array $rawData)
    {
        $this->type = $type;
        $this->actionType = $actionType;
        $this->rawData = $rawData;
    }

    public function add()
    {
        if ($this->type == 'hunt' || $this->type == 'event' || $this->type == 'practice') {
            $this->prepareData();
            $this->action();
        }else{
            throw new Exception("Invalid type provided for adding of minigames statistics.");
        }
    }

    public function prepareData()
    {
        $modelData = new MinigameHistory();
        $modelData->game_id = $this->rawData['game_id'];
        $modelData->time = (int) $this->rawData['time'];
        $modelData->score = (int) $this->rawData['score'];
        $modelData->from = $this->type;
        $modelData->action = $this->actionType;
        
        if ($this->type == 'hunt') {
        }else if($this->type == 'event') {
        }else if ($this->type == 'practice') {
            $modelData->random_mode = filter_var($this->rawData['random_mode'] , FILTER_VALIDATE_BOOLEAN);
        }
        return $this->data = $modelData;
    }

    public function action()
    {
        if ($this->actionType == 'completed' || $this->actionType == 'exited' || $this->actionType == 'failed') {
            $this->data->save();
        }else{
            throw new Exception("Invalid action type provided for adding of minigames statistics.");
        }
    }
}
