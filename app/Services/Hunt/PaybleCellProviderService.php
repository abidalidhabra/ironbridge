<?php

namespace App\Services\Hunt;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;

class PaybleCellProviderService
{
	
	protected $cellIDServerURL = 'http://54.152.124.171:8080/CELL2IDAPP/rest/json/metallica/get';
	protected $paybleGoogleKey = 'AIzaSyA_01wAGuFb4lEYCF2CO3zkKcFdDv2NORQ';
	protected $paybleGoogleURL;
	protected $client;
	protected $redis;
	protected $cell2ID;
	protected $cacheKeys;

	public function __construct()
	{
		$this->paybleGoogleURL = 'https://playablelocations.googleapis.com/v3:searchPlayableLocations?key='.$this->paybleGoogleKey;
		$this->client = new Client();
		$this->redis = Redis::connection();
	}

	public function getContents($result, $wantJSON = true, $wantFromCurl = true)
	{
		if ($wantFromCurl) {
			return ($wantJSON)? json_decode($result->getBody()->getContents()): $result->getBody()->getContents();
		}else{
			return json_decode($result);
		}
	}

	public function setCacheKeys()
	{
		$this->cacheKeys['minigames'] = $this->cell2ID.'.minigames';
		$this->cacheKeys['random_hunts'] = $this->cell2ID.'.random_hunts';
	}

	public function setCacheValues($for, $dataToSave)
	{
		if ($for == 'random_hunts' || $for == 'minigames') {
			$this->redis->set($this->cacheKeys[$for], $dataToSave, 'EX', 900);
		}else{
			throw new Exception("Invalid type provided for playable locations JSON.");
		}
	}

	public function getPlayableCellsJSONFile($for)
	{
		if ($for == 'random_hunts') {
			$file = 'playble-random-hunt-params.json';
		}else if ($for == 'minigames'){
			$file = 'playble-minigames-params.json';
		}else{
			throw new Exception("Invalid type provided for playable locations JSON.");
		}
		$fileContents = file_get_contents(storage_path('app/cell2Files/'.$file));
		$fileContents = str_replace('CELL2IDPLACE', $this->cell2ID, $fileContents);
		return $fileContents;
	}

	public function getCellID($data)
	{
		$cellIDResponse = $this->client->request('POST', $this->cellIDServerURL, ['body' => json_encode($data)]);
		$cellIDResponseJson = $this->getContents($cellIDResponse);
		if (empty($cellIDResponseJson)) {
			throw new Exception("Google Cell ID service is unavailable.");
		}
		$this->cell2ID = $cellIDResponseJson->cell2ID;
		$this->setCacheKeys();
		return $cellIDResponseJson;
	}

	public function getMinigamesCells()
	{
		return $this->getPlayableDataFromGoogle('minigames');
	}

	public function getRandomHuntsCells()
	{
		return $this->getPlayableDataFromGoogle('random_hunts');
	}

	public function getPlayableDataFromGoogle($getLocationsFor)
	{
		if ($cacheContents = $this->redis->get($this->cacheKeys[$getLocationsFor])) {
			return $this->getContents($cacheContents, true, false);;
		}else{
			$apiResponse = $this->client->request('POST', $this->paybleGoogleURL, ['body' => $this->getPlayableCellsJSONFile($getLocationsFor)]);
			$apiContents = $this->getContents($apiResponse, false);
			$this->setCacheValues($getLocationsFor, $apiContents);
			return $this->getContents($apiContents, true, false);
		}
	}
}