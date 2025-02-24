<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function sendSMS($to, $message)
    {
        return $this->client->messages->create(
            $to, // Recipient's phone number
            [
                'from' => env('TWILIO_PHONE_NUMBER'), // Your Twilio phone number
                'body' => $message,
            ]
        );
    }

    public function sendWhatsAppMessage($to, $message)
    {
        return $this->client->messages->create(
            "whatsapp:{$to}", // Recipient's WhatsApp number
            [
                'from' => "whatsapp:" . env('TWILIO_PHONE_NUMBER'), // Your Twilio WhatsApp number
                'body' => $message,
            ]
        );
    }
}
