<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TwilioWebhookController extends Controller
{
    public function handleIncomingMessage(Request $request)
    {
        $from = $request->input('From'); // Sender's WhatsApp number
        $body = $request->input('Body'); // Message content

        // Log the message for debugging
        Log::info("Incoming WhatsApp message from {$from}: {$body}");

        // Process the message (e.g., reply or save to database)
        $responseMessage = "You said: {$body}";

        // Send a reply back to the user
        $twilioService = new \App\Services\TwilioService();
        $twilioService->sendWhatsAppMessage($from, $responseMessage);

        return response('Message handled', 200);
    }
}
