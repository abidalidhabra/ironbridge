<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AdminNotificationChannel
{
    public function send($notifiable, Notification $notification)
    {

       // Send notification to the $notifiable instance...
     $data = $notification->toDatabase($notifiable);

     $notificationData['id']              = $notification->id;
     $tempNotificationType                = explode('\\', get_class($notification));
     $notificationData['type']            = end($tempNotificationType);
     $notificationData['notifiable_type'] = get_class($notifiable);
     $notificationData['data']            = $data;
     $notificationData['hide']            = false;

     $notificationType = get_class($notification);
     switch ($notificationType) {
         case 'App\\Notifications\\AdminPasswordSet':
         break;

     }
     return $notifiable->routeNotificationFor('database')->create($notificationData);
       // $queries = \DB::getQueryLog();
 }
}
