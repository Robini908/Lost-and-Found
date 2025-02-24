<?php

namespace App\Http\Controllers;

use App\Services\TwilioService;
use Illuminate\Http\Request;

class TwilioController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function sendSMS(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'required|string',
        ]);

        $response = $this->twilioService->sendSMS($request->to, $request->message);

        return response()->json([
            'status' => 'success',
            'message' => 'SMS sent successfully!',
            'response' => $response,
        ]);
    }

    public function sendTestSMS()
    {
        $to = '+254111592924'; // Recipient's phone number
        $message = 'This is a test SMS from Laravel and Twilio!'; // Test message

        // Send the SMS
        $response = $this->twilioService->sendSMS($to, $message);

        // Return a response
        return response()->json([
            'status' => 'success',
            'message' => 'Test SMS sent successfully!',
            'response' => $response,
        ]);
    }
    public function sendWhatsAppMessage(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'required|string',
        ]);

        $response = $this->twilioService->sendWhatsAppMessage($request->to, $request->message);

        return response()->json([
            'status' => 'success',
            'message' => 'WhatsApp message sent successfully!',
            'response' => $response,
        ]);
    }
}
