<?php

namespace App\Http\Controllers\Api\v2;

use App\Helpers\ResponseHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\v2\MarkTheEventMGAsCompleteRequest;
use App\Refacing\Contracts\EventsMiniGameRefaceInterface;
use App\Repositories\Contracts\EventsMiniGameInterface;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class EventsMiniGameController extends Controller
{
    
    private $eventsMiniGameInterface;
    private $eventsMiniGameRefaceInterface;

    public function __construct(
        EventsMiniGameInterface $eventsMiniGameInterface, 
        EventsMiniGameRefaceInterface $eventsMiniGameRefaceInterface)
    {
        $this->eventsMiniGameInterface = $eventsMiniGameInterface;
        $this->eventsMiniGameRefaceInterface = $eventsMiniGameRefaceInterface;
    }

    public function markTheEventMGAsComplete(MarkTheEventMGAsCompleteRequest $request)
    {
        try {

            /** prepare the data prior to insert **/
            $insertableData = $this->eventsMiniGameRefaceInterface->prepareCompletionDataToInsert($request->all());
            
            /** shot into the database **/
            $this->eventsMiniGameInterface->addCompletion($request->events_minigame_id, $request->minigame_unique_id, $insertableData);

            /** get the minigame's round status **/
            $miniGameRoundStatus = $this->eventsMiniGameInterface->getStatus($request->events_minigame_id);

            /** prepare output for the client **/
            $insertedData = $this->eventsMiniGameRefaceInterface->outputInsertedCompletionData(array_merge($insertableData, ['status'=> $miniGameRoundStatus->status], $request->only('events_minigame_id', 'minigame_unique_id')));

            return ResponseHelpers::successResponse($insertedData);
        } catch (Throwable $e) {
            return ResponseHelpers::errorResponse($e);
        } catch (Exception $e) {
            return ResponseHelpers::errorResponse($e);
        }
    }
}
