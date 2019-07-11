<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\v1\Avatar;
use App\Models\v1\ComplexityTarget;
use App\Models\v1\Game;
use App\Models\v1\Widget;
use App\Models\v1\WidgetItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrepareController extends Controller
{

	public function addGames(Request $Request){

		$games = [
			[
				"identifier" => "sudoku",
				"name" => "Sudoku"
			],
			[
				"name" => "Number search",
				"identifier" => "number_search"
			],
			[
				"identifier" => "jigsaw",
				"name" => "Jigsaw Puzzle"
			],
			[
				"identifier" => "sliding",
				"name" => "Sliding Puzzle"
			],
			[
				"identifier" => "word_search",
				"name" => "Word Search",
			],
			[
				"identifier" => "2048",
				"name" => "2048",
			],
			[
				"identifier" => "block",
				"name" => "Block Game",
			],
			[
				"identifier" => "hexa",
				"name" => "Hexa Puzzle"
			],
			[
				"identifier" => "bubble_shooter",
				"name" => "Bubble Shooter"
			],
			[
				"identifier" => "snake",
				"name" => "Snake"
			],
			[
				"identifier" => "domino",
				"name" => "Domino"
			],
			[
				"identifier" => "slices",
				"name" => "Slices"
			],
			[
				"identifier" => "yatzy",
				"name" => "Yatzy"
			]
		];

		Game::insert($games);
		return response()->json([
            'message'=>'Games has been added successfully.', 
        ],200);
	}

	public function addWidgets(Request $Request){

		$widgets = collect([
						['name'=> 'Hats', 'limit'=> 12], 
						['name'=> 'Tops', 'limit'=> 12], 
						['name'=> 'Bottom', 'limit'=> 12], 
						['name'=> 'Feets', 'limit'=> 12], 
						['name'=> 'Outfits', 'limit'=> 12]
					]);
		$avatars = Avatar::all();

		$avatars->map(function($avatar, $index) use ($widgets){
			
			// for ($i=0; $i < 5; $i++) { 
				
				if ($index == 1) {
					$widgets->map(function($widgetName, $index) use ($avatar){
						for ($i=0; $i < $widgetName['limit']; $i++) { 
							WidgetItem::create([
								'widget_name' => $widgetName['name'],
								'item_name' => $widgetName['name'].' '.rand(50,999),
								'gold_price' => rand(0,999),
								'avatar_id' => $avatar->_id
							]);
						}
						return $widgetName;
					});
				}
			// }
		});

		return response()->json([
            'message'=>'Widgets has been added successfully.', 
        ],200);
	}

	public function addComplexityWiseTarget(Request $Request){

		$games = Game::pluck('identifier', '_id')->toArray();
		$gamesData = collect([
			'sudoku' => [60, 55, 50, 45, 35],
			'number_search' => [15, 20, 25, 30, 35],
			'jigsaw' => [14, 35, 70, 70, 140],
			'sliding' => [4, 4, 5, 5, 6],
			'word_search' => [50, 80, 110, 140, 200],
			'2048' => [256, 256, 512, 1024, 2048],
			'block' => [600, 800, 1000, 1200, 1400],
			'hexa' => [300, 500, 700, 800, 1000],
			'bubble_shooter' => [500, 1000, 1500, 2000, 2500],
			'snake' => [10, 20, 30, 40, 50],
			'domino' => [200, 400, 600, 800, 1000],
			'slices' => [50, 80, 110, 140, 200],
			'yatzy' => [160, 180, 200, 220, 250]
		]);

		$gamesData->map(function($gameData, $index) use ($games){
			$identifierIndex = array_search($index, $games);
			for ($i=1; $i <= 5; $i++) { 
				ComplexityTarget::create([
					'game_id'=> $identifierIndex,
					'complexity'=> $i,
					'target'=> $gameData[$i-1]
				]);
			}
		});

		return response()->json([
            'message'=>'Complexity wise target has been added successfully.', 
        ],200);
	}
}
