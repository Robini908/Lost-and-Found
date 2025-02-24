<?php

namespace App\Livewire;

use Livewire\Component;
use App\Jobs\ProcessChatMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ChatInterfaces extends Component
{
    public $messages = [];
    public $newMessage = '';

    public function sendMessage()
    {
        if (!empty($this->newMessage)) {
            // Add the user's message to the chat
            $this->messages[] = ['sender' => 'user', 'text' => $this->newMessage];

            // Get the thread ID from the session (or create a new one)
            $threadId = Session::get('thread_id');

            // Dispatch the job to process the message
            ProcessChatMessage::dispatch($this->newMessage, $this, $threadId);

            // Clear the input field
            $this->newMessage = '';

            // Emit an event to trigger auto-scroll
            $this->dispatch('messageSent');
        }
    }

    public function messageReceived($response)
    {
        Log::info('Message received from assistant', ['response' => $response]);
        $this->messages[] = ['sender' => 'bot', 'text' => $response['response']];
        Session::put('thread_id', $response['thread_id']);
        $this->dispatch('messageSent');
    }

    public function messageError($error)
    {
        Log::error('Error in chat interface', ['error' => $error]);
        $this->messages[] = ['sender' => 'bot', 'text' => 'Sorry, something went wrong. Please try again later.'];
    }

    public function render()
    {
        return view('livewire.chat-interfaces');
    }
}