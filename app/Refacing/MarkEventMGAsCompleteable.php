<?php
namespace App\Refacing;

interface MarkEventMGAsCompleteInterface {

	public function prepareToInsert($eventUsersMiniGames);

	public function output($eventUsersMiniGames);
}