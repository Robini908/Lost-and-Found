<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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
            Log::info('Starting ProcessChatMessage job', ['message' => $this->message, 'threadId' => $this->threadId]);

            // OpenAI API endpoint for creating a thread and running the assistant
            $url = 'https://api.openai.com/v1/threads';

            // OpenAI API key from .env
            $apiKey = env('OPENAI_API_KEY');

            if (empty($apiKey)) {
                throw new \Exception('OpenAI API key is missing.');
            }

            // Use the existing thread ID or create a new thread
            if (!$this->threadId) {
                Log::info('Creating a new thread');
                $threadResponse = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                    'OpenAI-Beta' => 'assistants=v2', // Required for Assistants API
                ])->post($url);

                if (!$threadResponse->successful()) {
                    Log::error('Failed to create a thread', ['response' => $threadResponse->json()]);
                    throw new \Exception('Failed to create a thread.');
                }

                $this->threadId = $threadResponse->json()['id'];
                Session::put('thread_id', $this->threadId); // Save the thread ID to the session
                Log::info('Thread created', ['threadId' => $this->threadId]);
            }

            // Add the user's message to the thread
            Log::info('Adding message to thread', ['threadId' => $this->threadId, 'message' => $this->message]);
            $messageResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
                'OpenAI-Beta' => 'assistants=v2', // Required for Assistants API
            ])->post("{$url}/{$this->threadId}/messages", [
                'role' => 'user',
                'content' => $this->message,
            ]);

            if (!$messageResponse->successful()) {
                Log::error('Failed to add message to the thread', ['response' => $messageResponse->json()]);
                throw new \Exception('Failed to add message to the thread.');
            }

            // Run the assistant on the thread
            Log::info('Running assistant on thread', ['threadId' => $this->threadId]);
            $runResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
                'OpenAI-Beta' => 'assistants=v2', // Required for Assistants API
            ])->post("{$url}/{$this->threadId}/runs", [
                'assistant_id' => env('OPENAI_ASSISTANT_ID'), // Use the assistant ID
            ]);

            if (!$runResponse->successful()) {
                Log::error('Failed to run the assistant', ['response' => $runResponse->json()]);
                throw new \Exception('Failed to run the assistant.');
            }

            $runId = $runResponse->json()['id'];
            Log::info('Assistant run started', ['runId' => $runId]);

            // Wait for the run to complete
            do {
                sleep(1); // Wait for 1 second before checking the status
                Log::info('Checking run status', ['runId' => $runId]);
                $runStatusResponse = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                    'OpenAI-Beta' => 'assistants=v2', // Required for Assistants API
                ])->get("{$url}/{$this->threadId}/runs/{$runId}");

                if (!$runStatusResponse->successful()) {
                    Log::error('Failed to check run status', ['response' => $runStatusResponse->json()]);
                    throw new \Exception('Failed to check run status.');
                }

                $runStatus = $runStatusResponse->json()['status'];
                Log::info('Run status', ['status' => $runStatus]);

                // Handle failed or cancelled runs
                if ($runStatus === 'failed' || $runStatus === 'cancelled') {
                    throw new \Exception("Run failed or was cancelled. Status: {$runStatus}");
                }
            } while ($runStatus !== 'completed');

            // Retrieve the assistant's response
            Log::info('Retrieving assistant response', ['threadId' => $this->threadId]);
            $messagesResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
                'OpenAI-Beta' => 'assistants=v2', // Required for Assistants API
            ])->get("{$url}/{$this->threadId}/messages");

            if (!$messagesResponse->successful()) {
                Log::error('Failed to retrieve messages', ['response' => $messagesResponse->json()]);
                throw new \Exception('Failed to retrieve messages.');
            }

            // Extract the assistant's response
            $messages = $messagesResponse->json()['data'];
            $botResponse = $messages[0]['content'][0]['text']['value'];
            Log::info('Assistant response received', ['response' => $botResponse]);

            // Dispatch an event to update the chat interface
            $this->chatInterfaces->dispatch('messageReceived', [
                'response' => $botResponse,
                'thread_id' => $this->threadId, // Pass the thread ID back to the Livewire component
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ProcessChatMessage job', ['error' => $e->getMessage()]);
            $this->chatInterfaces->dispatch('messageError', ['error' => $e->getMessage()]);
        }
    }
}