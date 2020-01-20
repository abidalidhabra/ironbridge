<?php

namespace App\Helpers;

use DB;
use Auth;
use App\Models\v1\User;

class NotificationHelper{


	public static function sendPushNotification($receiver,$tag1,$msg="",$additionalData=[],$is_broadcast=false){

		$androidDeviceID = [];
		$iosDeviceID = [];

		$badgecount ="";
		switch ($tag1) {

			case 'adminNotification':
			$subject = "Bizbundle";
			$message = $msg;
			$badgecount = $additionalData['notificationcount'];
			$tag =$tag1;
			break;
		}




		$pushData 			  = [];
		$pushData['title'] 	  = $subject;
		$pushData['body'] 	  = $message;
		$pushData['priority'] = 10;
		$pushData['icon']  	  = "";
		$pushData['sound']    = 'mySound';		
		$pushData['badge'] 	  = (string)$badgecount;

		$payLoad 			  = [];
		$payLoad['title'] 	  = $subject;
		$payLoad['body']  	  = $message;
		$payLoad['tag']  	  = $tag;
		$payLoad['vibrate']   = 1;
		$payLoad['sound']  	  = 1;
		$payLoad['additional_data'] = $additionalData;


		if ($tag1 == 'adminNotification') {

			$androidDeviceID = $receiver->pluck('firebase_ids.android_id')->toArray();
			$androidDeviceID = array_values(array_filter($androidDeviceID));

			$iosDeviceID = $receiver->pluck('firebase_ids.ios_id')->unique()->toArray();;
			$iosDeviceID = array_values(array_filter($iosDeviceID));
			

		}

		if (count($androidDeviceID) > 0) {
			$fields = array('registration_ids' => $androidDeviceID,'data'=>$payLoad);
			$response = self::curlNotification($fields);
			// if ($response->failure) {
			// 	print_r($response->results);
			// 	exit;
			// }
			if ($tag1 =='blank_notification') {
			 	 return  json_encode($response);
                print_r($response);
                exit;
            }
		}

		if (count($iosDeviceID) > 0) {
			$fields = array('registration_ids' => $iosDeviceID,'notification'=> $pushData,'data'=>$payLoad);

			$response = self::curlNotification($fields);
			// if ($response->failure) {
			// 	print_r($response->results);
			// 	exit;
			// }
			 if ($tag1 =='blank_notification') {
			 	 return  json_encode($response);
                print_r($response);
                exit;
            }

		}
	}


	public static function curlNotification($fields){
		$headers = array('Authorization: key=AAAADHg-82U:APA91bG2eUR-c6DvJ0FUUiA8NOlahP6fTdixq4NeHNYCNoZKVKn0Vp1UTrUzjxEsl74r2A8UcOZHgrhTea3YAqdyAXNXlj58VQIE4l_ys-S-GGhMpD9ZoD040P5aJPJx0OBm8lh7xAhb','Content-Type: application/json');
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		curl_setopt($ch, CURLOPT_LOW_SPEED_LIMIT, 1);   // cancel cURL if below 1 byte/second
		curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, 3);   // Wait for 30 seconds
		$result = curl_exec($ch);		
		curl_close( $ch );
		return json_decode($result);
	}

}