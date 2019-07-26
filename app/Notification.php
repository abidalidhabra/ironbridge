<?php

namespace App;

use App\Collections\ReadCollection;
use Jenssegers\Mongodb\Eloquent\Model as Model;

class Notification extends Model
{
   protected $fillable = ['_id','type','notifiable_type','notifiable_id','data','read_at','hide'];

   protected $casts = [
   		'data' => 'array'
   ];

    protected $attributes = [
        'hide'    => false,
    ];

   protected $dates = [
        'read_at'
   ];
    /**
     * Mark the notification as read.
     *
     * @return void
    */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

    /**
     * Mark the notification as unread.
     *
     * @return void
    */
    public function markAsUnread()
    {
        if (! is_null($this->read_at)) {
            $this->forceFill(['read_at' => null])->save();
        }
    }

    /**
     * Determine if a notification has been read.
     *
     * @return bool
    */
    public function read()
    {
        return $this->read_at !== null;
    }

    /**
     * Determine if a notification has not been read.
     *
     * @return bool
    */
    public function unread()
    {
        return $this->read_at === null;
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new ReadCollection($models);
    }
}
