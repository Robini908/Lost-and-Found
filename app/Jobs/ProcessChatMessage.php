<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Livewire\ChatInterfaces;

class ProcessChatMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $chatInterfaces;
    protected $threadId;

    public function __construct($message, ChatInterfaces $chatInterfaces, $threadId = null)
    {
        $this->message = $message;
        $this->chatInterfaces = $chatInterfaces;
        $this->threadId = $threadId;
    }

    public function handle()
    {
        try {
            // Get API key from config
            $apiKey = config('services.huggingface.key');

            // Debug API key (mask it for security)
            Log::debug('API Key Check', [
                'has_key' => !empty($apiKey),
                'key_start' => $apiKey ? substr($apiKey, 0, 4) : null,
                'key_length' => $apiKey ? strlen($apiKey) : 0
            ]);

            if (empty($apiKey)) {
                throw new \Exception('Hugging Face API key not configured');
            }

            // Use a simpler model for testing
            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api-inference.huggingface.co/models/gpt2', [
                'inputs' => $this->message,
                'parameters' => [
                    'max_length' => 50,
                    'temperature' => 0.7
                ]
            ]);

            Log::debug('API Response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if (!$response->successful()) {
                throw new \Exception('API request failed: ' . $response->body());
            }

            $result = $response->json();

            // Extract the response text
            $botResponse = is_array($result) && !empty($result[0]) ?
                          $result[0]['generated_text'] :
                          'Sorry, I could not generate a response.';

            // Send response back to chat interface
            $this->chatInterfaces->dispatch('messageReceived', [
                'response' => $botResponse,
                'thread_id' => $this->threadId ?? uniqid()
            ]);

        } catch (\Exception $e) {
            Log::error('Chat Processing Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->chatInterfaces->dispatch('messageError', [
                'error' => 'Sorry, I encountered an error. Please try again.'
            ]);
        }
    }
}
