<?php

namespace App\Http\Traits;
use App\Models\NotificationMessage;
use App\Models\Notification;
use PushNotification;
use App\Models\User;
use App\Models\Role;

trait SendPushNotification
{
    /**
	 * Common function to send notification to users
	 */
    private function sendUserPushNotification($notificationData = [])
    {
        // Get users
        $authUser = auth()->user();
        //----------

        $message = null;
        $title = null;
        $notification = null;
        $response = false;

        if (count($notificationData)) {

            $userName = isset($notificationData['user_name']) ? $notificationData['user_name'] : null;
            $planName = isset($notificationData['plan_name']) ? $notificationData['plan_name'] : null;
            $planStartDateTime = isset($notificationData['plan_start_date']) ? $notificationData['plan_start_date'] : null;
            $planEndDateTime = isset($notificationData['plan_end_date']) ? $notificationData['plan_end_date'] : null;
            $planAmount = isset($notificationData['plan_amount']) ? $notificationData['plan_amount'] : null;
            $planExpireDate = isset($notificationData['plan_expiry_date']) ? $notificationData['plan_expiry_date'] : null;
            
            // Get message
            $messageTags = config('notification_constants.notification_tags');
            $notificationMessage = config('notification_constants.notification_messages.PUSH.'.$notificationData['type']);

            if(!empty($notificationMessage)) {
                $replaceTags = [
                    $userName, $planName, $planStartDateTime, $planEndDateTime, $planAmount, $planExpireDate
                ];

                $message = str_replace($messageTags, $replaceTags, $notificationMessage['message']);
                $title = $notificationMessage['title'];

                // Create notification
                if(!empty($message) && !empty($title)) 
                {
                    // Send Notification
                    if(isset($notificationData['user_fcm_token']) && !empty($notificationData['user_fcm_token'])){

                        if(env("NOTIFICATION_TYPE") == 'FCM') {
                            $notiArray = array(
                                'notification' => [
                                    'title' => $title,
                                    'body'  => $message,
                                ],
                                'data' => [
                                    'click_action'      => 'FLUTTER_NOTIFICATION_CLICK',
                                    'notification_type' => $notificationData['notification_type'] ?? null,
                                    'user_id'           => $notificationData['user_id'],
                                    'plan_id'           => $notificationData['plan_id']
                                ]
                            );
                            
                            $pushResponse = PushNotification::setService('fcm')
                            ->setMessage($notiArray)
                            ->setApiKey(env("FCM_SERVER_KEY"))
                            ->setDevicesToken($notificationData['user_fcm_token'])
                            ->send()
                            ->getFeedback();

                            if($pushResponse->success == 1){
                                $response = true;
                            }
                        }
                        
                        return $response;
                    } else {
                        return $response;
                    }
                    //------------------
                }
                //--------------------
            } else {
                return $response;
            }
            //------------
            
        } else {
            return $response;
        }

    }
}
