<?php
namespace App\Exceptions\PracticeMiniGame;

use App\Exceptions\PracticeMiniGame\CompletionTimesTrait;
use Exception;

class FreezeModeRunningException extends Exception {

	use CompletionTimesTrait;
}