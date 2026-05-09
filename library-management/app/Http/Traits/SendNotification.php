<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\ThirdPartyIntegration;
use App\Models\OneTimePassword;
use App\Models\SMSTemplate;
use PushNotification;

trait SendNotification
{
    /**
	 * Common function to send notification
	 */
    private function sendAllNotification($email = [], $sms = [], $push = [])
    {
        $notificationResponse = [
            'isMailSent' => 0,
            'isSmsSent' => 0,
            'isPushSent' => 0
        ];
        
		// Upload image
		try {
			
            if(!(empty($email)))
            {
                // Call send email function
                $notificationResponse['isMailSent'] = $this->sendEmail($email);
            }

            if(!(empty($sms)))
            {
                // Call send email function
                $notificationResponse['isSmsSent'] = $this->sendSMS($sms);
            }

            if(!(empty($push)))
            {
                // Call send push function
                $notificationResponse['isPushSent'] = $this->sendPush($push);
            }

		} catch (\Exception $e) {
			//
		}

		return $notificationResponse;
    }

    private function sendEmail($email)
    {
        $mailResponse = Mail::send($email['template']['email_template'], $email, function($message) use($email) {
            $message->subject($email['mail']['subject']);
            $message->to($email['mail']['to']);

            if(!(empty($email['mail']['cc'])))
            {
                $message->cc($email['mail']['cc']);
            }

            if(!(empty($email['mail']['bcc'])))
            {
                $message->bcc($email['mail']['bcc']);
            }
        });

        if($mailResponse){
            return true;
        }
        else if(count(Mail::failures()) > 0)
        {
            return false;
        }
        else{
            return false;
        }
    }

    private function sendSMS($smsData)
    {
        $responseSms = false;

        if(!isset($smsData['mobile_number']) || empty($smsData['mobile_number']))
        {
            return $responseSms;
        }

        // Get Credentials
        $sms = ThirdPartyIntegration::where('name', 'sms')->first();
        $sms = json_decode($sms->details, true);

        // Get Template
        $smsTemplate = SMSTemplate::where('id', $smsData['template_id'])->first();

        switch($smsData['template_id']){
            case 1:
                
                // Store one time password
                $one_time_password = OneTimePassword::updateOrCreate([
                    'mobile_number'     => $smsData['mobile_number'],
                ], [
                    'user_id'           => 1,
                    'one_time_password' => rand(100000, 999999),
                    'type'              => config('constants.one_time_password_types.VERIFICATION.value'),
                    'request_token'     => (string) Str::uuid(),
                    'expires_at'        => Carbon::now()->addHours(24)
                ]);
                //------------------------

                $OneTimePassword = $one_time_password->one_time_password;

                $messageTags = config('constants.notification_tags.OneTimePassword');
                $replaceTags = [
                    $OneTimePassword,
                ];

                $message = str_replace($messageTags, $replaceTags, $smsTemplate->message);

                break;
            
            case 2:
                break;
                
            case 3:

                $messageTags = config('constants.notification_tags.InvitationLink');
                $replaceTags = $smsData['replace_tags'];

                $message = str_replace($messageTags, $replaceTags, $smsTemplate->message);

                break;
            default:
        }

        if($sms['type'] == 'smsidea')
        {
            $mobileNumber = '91'.$smsData['mobile_number'];
            // $response     = file_get_contents($sms['url']."?mobile=".$sms['username']."&pass=".$sms['password']."&senderid=".$sms['sender']."&to=".htmlentities($mobileNumber)."&msg=".urlencode($message));

            $responseSms = true;
        }

        return $responseSms;
    }

    private function sendPush($push)
    {
        $response = false;

        if(env("NOTIFICATION_TYPE") == 'FCM') {
            $notiArray = array(
                'data' => $push
            );

            // Push notification object
            $pushResponse = PushNotification::setService('fcm');

            if(isset($push['is_topic_notification']) && $push['is_topic_notification'] == true)
            {
                $pushResponse = $pushResponse->setMessage($notiArray)
                ->setApiKey(env("FCM_SERVER_KEY"))
                ->setConfig(['dry_run' => false])
                ->sendByTopic($push['topic'])
                ->getFeedback();
            }
            else
            {
                $pushResponse = $pushResponse->setMessage($notiArray)
                ->setApiKey(env("FCM_SERVER_KEY"))
                ->setDevicesToken($push['data']['user_fcm_token'])
                ->send()
                ->getFeedback();
            }

            if($pushResponse->success == 1){
                $response = true;
            }
        }

        return $response;
    }
}
