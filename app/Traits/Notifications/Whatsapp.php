<?php

namespace App\Traits\Notifications;
use App\Providers\RouteServiceProvider;
use Twilio\Rest\Client;

trait Whatsapp
{
    public function getCredential()
    {
        $data['sid'] = config('services.twilio.sid');
        $data['token'] = config('services.twilio.token');
        $data['from'] = config('services.twilio.whatsapp_from');
        return $data;
    }

    public function sendWhatsappMessage($recipientNumber,$message)
    {
        $credential = $this->getCredential();
        $twilio = new Client($credential['sid'], $credential['token']);

        try {
            $twilio->messages->create(
                $recipientNumber,
                [
                    "from" => $credential['from'],
                    "body" => $message,
                ]
            );

            return true;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
