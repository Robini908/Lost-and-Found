<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        // This endpoint is no longer needed for OpenAI integration
        return response()->json(['reply' => 'This endpoint is deprecated.']);
    }
}
