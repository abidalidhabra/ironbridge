<?php

namespace App\Http\Controllers\Api\v2;

use App\Factories\WidgetItemFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\v2\UnlockWidgetItemRequest;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\Contracts\WidgetItemInterface;
use Illuminate\Http\Request;

class WidgetItemController extends Controller
{
    private $widgetItemInterface;
    private $userInterface;
    private $user;

    public function __construct(WidgetItemInterface $widgetItemInterface)
    {
        $this->widgetItemInterface = $widgetItemInterface;
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            $this->userInterface = app(UserInterface::class)($this->user);
            return $next($request);
        });
    }

    public function unlockWidgetItem(UnlockWidgetItemRequest $request)
    {

        $widgetItem = $this->widgetItemInterface->find($request->widget_item_id);

        $WidgetItemFactory = new WidgetItemFactory($this->user);
        if (!$widgetItem->free || $this->user->free_outfit_taken) {
            $availableGold = $this->userInterface->deductGold($widgetItem->gold_price);
            $data = $WidgetItemFactory->initializeWidgetItem($widgetItem);
        }else {
            $availableGold = $this->user->gold_balance;
            $data = $WidgetItemFactory->resetWidgetItem($widgetItem);
        }


        return response()->json([
            'message' => 'Your Widget has been unlocked successfully.',
            'remaining_coins' => $availableGold,
            'widget_name'    => $widgetItem->widget_name,
            'widget_item_id' => $data
        ]);
    }

    public function updatedWidgetData()
    {
        $data = [
            [ 
                'id'=> "5d246f230b6d7b1a0a232482",
                'name'=> "Female Adventure",
                'items'=> [ 
                    "5d246f230b6d7b1a0a23245e", 
                    "5d246f230b6d7b1a0a23246a",
                    "5d246f230b6d7b1a0a232453",
                    "5d246f230b6d7b1a0a232476",
                ]
            ],
            [ 
                'id'=> "5d246f230b6d7b1a0a232483",
                'name'=> "Female Adventure 2",
                'items'=> [ 
                    "5d246f230b6d7b1a0a23245f", 
                    "5d246f230b6d7b1a0a23246b",
                    "5d246f230b6d7b1a0a232452",
                    "5d246f230b6d7b1a0a232477",
                ]
            ],   
            [ 
                'id'=> "5d246f230b6d7b1a0a232483",
                'name'=> "Female Adventure Deluxe 1",
                'items'=> [ 
                    "5d246f230b6d7b1a0a23247c", 
                    "5d246f230b6d7b1a0a232458",
                    "5d246f230b6d7b1a0a232464",
                    "5d246f230b6d7b1a0a232470",
                ]
            ],  
            [ 
                'id'=> "5d246f230b6d7b1a0a232489",
                'name'=> "Female Adventure Deluxe 2",
                'items'=> [ 
                    "5d246f230b6d7b1a0a23247d", 
                    "5d246f230b6d7b1a0a232459",
                    "5d246f230b6d7b1a0a232465",
                    "5d246f230b6d7b1a0a232471",
                ]
            ],
            [ 
                'id'=> "5d246f230b6d7b1a0a232485",
                'name'=> "Female Future 1",
                'items'=> [ 
                    "5d246f230b6d7b1a0a23246c", 
                    "5d246f230b6d7b1a0a232455",
                    "5d246f230b6d7b1a0a232460",
                    "5d246f230b6d7b1a0a232479",
                ]
            ],
            [ 
                'id'=> "5d246f230b6d7b1a0a232484",
                'name'=> "Female Future 2",
                'items'=> [ 
                    "5d246f230b6d7b1a0a23246d", 
                    "5d246f230b6d7b1a0a232454",
                    "5d246f230b6d7b1a0a232461",
                    "5d246f230b6d7b1a0a232478",
                ]
            ],
            [ 
                'id'=> "5d246f230b6d7b1a0a23248a",
                'name'=> "Female Future Deluxe 1",
                'items'=> [ 
                    "5d246f230b6d7b1a0a23245a", 
                    "5d246f230b6d7b1a0a23247e",
                    "5d246f230b6d7b1a0a232466",
                    "5d246f230b6d7b1a0a232472",
                ]
            ],
            [ 
                'id'=> "5d246f230b6d7b1a0a23248b",
                'name'=> "Female Future Deluxe 2",
                'items'=> [ 
                    "5d246f230b6d7b1a0a23245b", 
                    "5d246f230b6d7b1a0a23247f",
                    "5d246f230b6d7b1a0a232467",
                    "5d246f230b6d7b1a0a232473",
                ]
            ],
            [ 
                'id'=> "5d246f230b6d7b1a0a232486",
                'name'=> "Female Victorian 1",
                'items'=> [ 
                    "5d246f230b6d7b1a0a23246e", 
                    "5d246f230b6d7b1a0a23247a",
                    "5d246f230b6d7b1a0a232456",
                    "5d246f230b6d7b1a0a232462",
                ]
            ],
            [ 
                'id'=> "5d246f230b6d7b1a0a232487",
                'name'=> "Female Victorian 2",
                'items'=> [ 
                    "5d246f230b6d7b1a0a23246f", 
                    "5d246f230b6d7b1a0a23247b",
                    "5d246f230b6d7b1a0a232457",
                    "5d246f230b6d7b1a0a232463",
                ]
            ],
            [ 
                'id'=> "5d246f230b6d7b1a0a23248c",
                'name'=> "Female Victorian Deluxe 1",
                'items'=> [ 
                    "5d246f230b6d7b1a0a23245c", 
                    "5d246f230b6d7b1a0a232468",
                    "5d246f230b6d7b1a0a232475",
                    "5d246f230b6d7b1a0a232480"
                ]
            ],
            [ 
                'id'=> "5d246f230b6d7b1a0a23248d",
                'name'=> "Female Victorian Deluxe 2",
                'items'=> [ 
                    "5d246f230b6d7b1a0a23245d", 
                    "5d246f230b6d7b1a0a232469",
                    "5d246f230b6d7b1a0a232474",
                    "5d246f230b6d7b1a0a232481"
                ]
            ],
            [ 
                'id'=> "5d246f0c0b6d7b19fb5ab590",
                'name'=> "Male Adventure 1",
                'items'=> [ 
                    "5d246f0c0b6d7b19fb5ab56d", 
                    "5d246f0c0b6d7b19fb5ab562",
                    "5d246f0c0b6d7b19fb5ab578",
                    "5d246f0c0b6d7b19fb5ab584",
                ]
            ],
            [ 
                'id'=> "5d246f0c0b6d7b19fb5ab591",
                'name'=> "Male Adventure 2",
                'items'=> [ 
                    "5d246f0c0b6d7b19fb5ab56c", 
                    "5d246f0c0b6d7b19fb5ab563",
                    "5d246f0c0b6d7b19fb5ab579",
                    "5d246f0c0b6d7b19fb5ab585",
                ]
            ],
            [ 
                'id'=> "5d246f0c0b6d7b19fb5ab596",
                'name'=> "Male Adventure Deluxe 1",
                'items'=> [ 
                    "5d246f0c0b6d7b19fb5ab57e", 
                    "5d246f0c0b6d7b19fb5ab58a",
                    "5d246f0c0b6d7b19fb5ab572",
                ]
            ],
            [ 
                'id'=> "5d246f0c0b6d7b19fb5ab597",
                'name'=> "Male Adventure Deluxe 2",
                'items'=> [ 
                    "5d246f0c0b6d7b19fb5ab57f", 
                    "5d246f0c0b6d7b19fb5ab58b",
                    "5d246f0c0b6d7b19fb5ab573",
                ]
            ],
            [ 
                'id'=> "5d246f0c0b6d7b19fb5ab592",
                'name'=> "Male Future 1",
                'items'=> [ 
                    "5d246f0c0b6d7b19fb5ab56e", 
                    "5d246f0c0b6d7b19fb5ab57a",
                    "5d246f0c0b6d7b19fb5ab564",
                    "5d246f0c0b6d7b19fb5ab586",
                ]
            ],
            [ 
                'id'=> "5d246f0c0b6d7b19fb5ab593",
                'name'=> "Male Future 2",
                'items'=> [ 
                    "5d246f0c0b6d7b19fb5ab56f", 
                    "5d246f0c0b6d7b19fb5ab57b",
                    "5d246f0c0b6d7b19fb5ab565",
                    "5d246f0c0b6d7b19fb5ab587",
                ]
            ],
            [ 
                'id'=> "5d246f0c0b6d7b19fb5ab599",
                'name'=> "Male Future Deluxe 1",
                'items'=> [ 
                    "5d246f0c0b6d7b19fb5ab58c", 
                    "5d246f0c0b6d7b19fb5ab568",
                    "5d246f0c0b6d7b19fb5ab574",
                    "5d246f0c0b6d7b19fb5ab580",
                ]
            ],
            [ 
                'id'=> "5d246f0c0b6d7b19fb5ab598",
                'name'=> "Male Future Deluxe 2",
                'items'=> [ 
                    "5d246f0c0b6d7b19fb5ab58d", 
                    "5d246f0c0b6d7b19fb5ab569",
                    "5d246f0c0b6d7b19fb5ab575",
                    "5d246f0c0b6d7b19fb5ab581",
                ]
            ],
            [ 
                'id'=> "5d246f0c0b6d7b19fb5ab594",
                'name'=> "Male Victorian 1",
                'items'=> [ 
                    "5d246f0c0b6d7b19fb5ab57c", 
                    "5d246f0c0b6d7b19fb5ab566",
                    "5d246f0c0b6d7b19fb5ab570",
                    "5d246f0c0b6d7b19fb5ab589",
                ]
            ],
            [ 
                'id'=> "5d246f0c0b6d7b19fb5ab595",
                'name'=> "Male Victorian 2",
                'items'=> [ 
                    "5d246f0c0b6d7b19fb5ab57d", 
                    "5d246f0c0b6d7b19fb5ab567",
                    "5d246f0c0b6d7b19fb5ab571",
                    "5d246f0c0b6d7b19fb5ab588",
                ]
            ],
            [ 
                'id'=> "5d246f0c0b6d7b19fb5ab59a",
                'name'=> "Male Victorian Deluxe 1",
                'items'=> [ 
                    "5d246f0c0b6d7b19fb5ab56a", 
                    "5d246f0c0b6d7b19fb5ab58e",
                    "5d246f0c0b6d7b19fb5ab576",
                    "5d246f0c0b6d7b19fb5ab582",
                ]
            ],
            [ 
                'id'=> "5d246f0c0b6d7b19fb5ab59b",
                'name'=> "Male Victorian Deluxe 2",
                'items'=> [ 
                    "5d246f0c0b6d7b19fb5ab56b", 
                    "5d246f0c0b6d7b19fb5ab58f",
                    "5d246f0c0b6d7b19fb5ab577",
                    "5d246f0c0b6d7b19fb5ab583",
                ]
            ],
            [
                'id'=> "5d246f230b6d7b1a0a232488",
                'name'=> "Female Adventure Deluxe 1",
                'items'=> [ 
                    "5d246f230b6d7b1a0a23247c", 
                    "5d246f230b6d7b1a0a232458",
                    "5d246f230b6d7b1a0a232464",
                    "5d246f230b6d7b1a0a232470",
                ] 
            ]
        ];

        foreach ($data as $key => $data) {
            // $data['items'][] = $data['id'];
            $status = \App\Models\v1\WidgetItem::where(['_id'=> $data['id']])->update(['items'=> $data['items']]);
        }
        return response()->json($data);
    }
}
