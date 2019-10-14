<?php

namespace App\Events\MiniGames\Factory;

use App\Events\MiniGames\Completed;
use App\Events\MiniGames\Exited;
use App\Events\MiniGames\Failed;

class MinigamesEventFactory
{

    private $type;
    private $actionType;
    private $data;

    public function __construct($type, $actionType, $data)
    {
        $this->type = $type;
        $this->actionType = $actionType;
        $this->data = $data;
    }
    public function handle()
    {
        if ($this->type == 'hunt') {
            $this->action();
        }else if($this->type == 'event') {
            $this->action();
        }else if ($this->type == 'practice') {
            $this->action();
        }
        throw new Exception("Invalid type provided for adding of minigames statistics.");
    }

    public function action()
    {
        if ($this->actionType == 'completed') {
            event(new Completed($this->data));
            exit;
        }else if($this->actionType == 'exited') {
            event(new Failed($this->data));
        }else if ($this->actionType == 'failed') {
            event(new Exited($this->data));
        }
        throw new Exception("Invalid action type provided for adding of minigames statistics.");
    }
}
