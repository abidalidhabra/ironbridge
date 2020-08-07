<?php

namespace App\Services\Event;

use App\Notifications\EventNotification;
use App\Repositories\User\UserRepository;
use App\v3\FCMNotificationsHistory;
use Illuminate\Support\Facades\Notification;
use MongoDB\BSON\UTCDateTime;

class EventNotificationService
{

	public $notification;
	public $users;
	public $isPreSchedule = true;
	
	public function setNotification($notification)
	{
		$this->isPreSchedule = false;
		$this->notification = $notification;
		return $this;
	}

	public function notification()
    {
        $this->notification = FCMNotificationsHistory::where('send_at', '<=', new UTCDateTime())->pending()->first();
    }

    public function users()
    {

        $target = $this->notification->target;
        $targetAudience = $this->notification->target_audience;

        /** Send the message to local people leaving in cities mentioned in cities parameter. **/
        $msgToLocalsByCityIds = ($target == 'BYCITY' && $targetAudience == 'LOCALS')? true: false;
        
        /** Send the message to local people leaving in country mentioned in countries parameter. **/
        $msgToLocalsByCountyIds = ($target == 'BYCOUNTRY' && $targetAudience == 'LOCALS')? true: false;
        
        /** Send the message to people which are not leaving in cities mentioned in cities parameter. **/
        $msgToOutsidersByCityIds = ($target == 'BYCITY' && $targetAudience == '!LOCALS')? true: false;
        
        /** Send the message to people which are not leaving in cities mentioned in countries parameter. **/
        $msgToOutsidersByCountyIds = ($target == 'BYCOUNTRY' && $targetAudience == '!LOCALS')? true: false;

        $this->users = (new UserRepository)->getModel()
                ->when($msgToLocalsByCityIds, function($query) {
                    $query->whereIn('city_id', $this->notification->cities);
                })
                ->when($msgToLocalsByCountyIds, function($query) {
                    $query->whereHas('city.country', function($query) {
                        return $query->whereIn('_id', $this->notification->countries);
                    });
                })
                ->when($msgToOutsidersByCityIds, function($query) {
                    $query->whereNotIn('city_id', $this->notification->cities);
                })
                ->when($msgToOutsidersByCountyIds, function($query) {
                    $query->doesntHave('city.country', function($query) {
                        return $query->whereIn('_id', $this->notification->countries);
                    });
                })
                ->select('_id','first_name','last_name','firebase_ids','device_info')
                ->get();
    }

    public function send()
    {
    	\Log::info($this->users->toArray());
        Notification::send($this->users, new EventNotification($this->notification->title, $this->notification->message));
    }

    public function handle()
    {
    	if (!$this->notification) {
        	$this->notification();
    	}

    	if ($this->notification) {
	        $this->users();
	        $this->send();
	        
	        if ($this->isPreSchedule) {
	        	$this->notification->status = 'sent';
	        	$this->notification->save();
	        }
    	}
    	return $this;
    }
}