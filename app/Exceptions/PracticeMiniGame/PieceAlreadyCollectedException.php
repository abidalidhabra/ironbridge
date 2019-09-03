<?php
namespace App\Exceptions\PracticeMiniGame;

use App\Exceptions\PracticeMiniGame\CompletionTimesTrait;
use Exception;

class PieceAlreadyCollectedException extends Exception {

	use CompletionTimesTrait;
}