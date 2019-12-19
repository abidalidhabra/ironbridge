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
	protected $cell2IDs;

	public function __construct()
	{
		$this->paybleGoogleURL = 'https://playablelocations.googleapis.com/v3:searchPlayableLocations?key='.$this->paybleGoogleKey;
		$this->client = new Client();
		$this->redis = Redis::connection();
		$this->cell2IDs = collect();
	}

	public function setCell2ID($cell2ID)
	{
		$this->cell2ID = $cell2ID;
		return $this;
	}

	public function addCell2IDs($cell2ID)
	{
        $this->cell2IDs->push($cell2ID);
		return $this;
	}

	public function getCell2IDs()
	{
        return $this->cell2IDs;
	}

	public function getContents($result, $wantJSON = true, $wantFromCurl = true)
	{
		if ($wantFromCurl) {
			return ($wantJSON)? json_decode($result->getBody()->getContents()): $result->getBody()->getContents();
		}else{
			return json_decode($result);
		}
	}

	public function setCacheableKeys()
	{
		$this->cacheKeys['minigames'] = $this->cell2ID.'.minigames';
		$this->cacheKeys['random_hunts'] = $this->cell2ID.'.random_hunts';
	}

	public function setCacheableValues($for, $dataToSave)
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
		$this->setCacheableKeys();
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
			$this->setCacheableValues($getLocationsFor, $apiContents);
			return $this->getContents($apiContents, true, false);
		}
	}

	// public function getCellIDs($request, $user)
	// {
	// 	$response = [];

	// 	foreach (json_decode($request->coordinates, true) as $coordinate) {

	// 		$cell2ID = $this->getCellID(
	// 						array_merge($coordinate, ['level'=> $request->level])
	// 					);

	// 		if (!$this->cell2IDs->contains('cell2ID', $cell2ID->cell2ID)) {
				
	// 			$responseToBeGiven = [];
	// 			$responseToBeGiven['cell2ID'] = $cell2ID;
	// 			$this->cell2IDs->push($cell2ID);
	// 			$responseToBeGiven['random_hunt_cell'] = $this->getRandomHuntsCells();
				
	// 			if ($user->nodes_status && (isset($user->nodes_status['mg_challenge']) || isset($user->nodes_status['power']) || isset($user->nodes_status['bonus']))) {

	// 				$playableRes = $this->getMinigamesCells(); 
	// 				$playableNodes = collect($playableRes->locationsPerGameObjectType); 

	// 				if (isset($user->nodes_status['power'])) {  
	// 					$responseToBeGiven['power_station_node'] = $playableNodes->first();  
	// 				}   

	// 				if (isset($user->nodes_status['mg_challenge'])) {   
	// 					$responseToBeGiven['minigame_node'] = $playableNodes->slice(1)->take(1)->first();    
	// 				}
	// 				if (isset($user->nodes_status['bonus'])) {    

	// 					$responseToBeGiven['bonus_nodes'] = $playableNodes->slice(2)->values();  
	// 				}   
	// 			}
	// 		}

	// 		$response[] = $responseToBeGiven;
	// 	}
	// 	return $response;
	// }
}