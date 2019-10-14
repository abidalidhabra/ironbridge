<?php

namespace App\Factories;

use App\Events\MiniGame\Completed;
use App\Events\MiniGame\Exited;
use App\Events\MiniGame\Failed;
use App\Models\v2\MinigameHistory;
use App\Repositories\Hunt\HuntUserDetailRepository;
use App\Repositories\MiniGameRepository;
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
        $modelData->from = $this->type;
        $modelData->action = $this->actionType;
        
        if ($this->type == 'hunt') {
            if ($this->actionType == 'completed') {
                $modelData->hunt_user_detail_id = $this->rawData['hunt_user_detail_id'];
                $modelData->game_id = $this->rawData['game_id'];
                $modelData->time = $this->rawData['time'];
                $modelData->complexity = $this->rawData['complexity'];
            }else{
                $huntUserDetail = (new HuntUserDetailRepository)->find($this->rawData['hunt_user_details_id'], ['_id', 'game_id', 'complexity','hunt_user_id', 'finished_in']);
                $modelData->hunt_user_detail_id = $this->rawData['hunt_user_details_id'];
                $modelData->game_id = $huntUserDetail->game_id;
                $modelData->time = $huntUserDetail->finished_in;
                $modelData->complexity = $huntUserDetail->hunt_user->complexity;
            }
        }else if($this->type == 'event') {
        }else if ($this->type == 'practice') {
            $practiceGameUser = (new MiniGameRepository(auth()->user()))->find($this->rawData['practice_game_user_id'], ['_id', 'game_id']);
            $modelData->practice_game_user_id = $this->rawData['practice_game_user_id'];
            $modelData->score = (int) $this->rawData['score'];
            $modelData->time = (int) $this->rawData['time'];
            $modelData->game_id = $practiceGameUser->game_id;
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
