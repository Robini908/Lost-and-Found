<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class ChatInterfaces extends Component
{
    public $messages = [];
    public $newMessage = '';

    public function mount()
    {
        $this->messages = Session::get('chat_messages', []);

        // Add welcome message if no messages exist
        if (empty($this->messages)) {
            $this->addSystemMessage("Hello! 👋 I'm your Lost & Found Assistant. How can I help you today?");
        }
    }

    public function sendMessage()
    {
        if (empty(trim($this->newMessage))) {
            return;
        }

        try {
            // Add user message
            $this->messages[] = [
                'sender' => 'user',
                'text' => $this->newMessage
            ];

            // Get bot response
            $response = $this->processMessage($this->newMessage);

            // Clear input and save user message
            $this->newMessage = '';

            // Add bot response
            $this->messages[] = [
                'sender' => 'bot',
                'text' => $response
            ];

            // Save to session
            Session::put('chat_messages', $this->messages);

            // Trigger scroll to bottom
            $this->dispatch('messageSent');

        } catch (\Exception $e) {
            $this->addSystemMessage('Sorry, something went wrong. Please try again.');
        }
    }

    protected function processMessage($message)
    {
        $message = strtolower($message);

        // Define response patterns
        $patterns = [
            'lost' => [
                'keywords' => ['lost', 'missing', 'cant find', "can't find", 'looking for'],
                'response' => "I can help you find your lost item. Please tell me:\n\n1️⃣ What did you lose?\n2️⃣ Where did you last see it?\n3️⃣ When did you lose it?\n\nClick here to 👉 <button wire:click=\"redirectTo('products.report-item')\" class='text-blue-500 hover:underline inline-flex items-center'>Report Lost Item <i class='fas fa-arrow-right ml-1'></i></button>"
            ],
            'found' => [
                'keywords' => ['found', 'discovered', 'picked up', 'located'],
                'response' => "Thank you for reporting a found item! Please provide:\n\n1️⃣ What did you find?\n2️⃣ Where did you find it?\n3️⃣ When did you find it?\n\nClick here to 👉 <button wire:click=\"redirectTo('products.report-found-item')\" class='text-blue-500 hover:underline inline-flex items-center'>Report Found Item <i class='fas fa-arrow-right ml-1'></i></button>"
            ],
            'status' => [
                'keywords' => ['status', 'check', 'claim', 'update'],
                'response' => "To check your claim status, click here 👉 <button wire:click=\"redirectTo('claims.index')\" class='text-blue-500 hover:underline inline-flex items-center'>View Claims <i class='fas fa-arrow-right ml-1'></i></button>"
            ],
            'help' => [
                'keywords' => ['help', 'support', 'guide', 'how'],
                'response' => "I can help you with:\n\n📱 Reporting lost items\n📦 Reporting found items\n🔍 Checking claim status\n💬 Contacting support\n\nWhat would you like to know more about?"
            ],
            'thanks' => [
                'keywords' => ['thank', 'thanks', 'thx', 'appreciate'],
                'response' => "You're welcome! 😊 Is there anything else I can help you with?"
            ],
            'hello' => [
                'keywords' => ['hi', 'hello', 'hey', 'howdy', 'greetings'],
                'response' => "Hello! 👋 How can I assist you today with lost and found items?"
            ]
        ];

        // Check for matches
        foreach ($patterns as $type => $data) {
            foreach ($data['keywords'] as $keyword) {
                if (str_contains($message, $keyword)) {
                    return $data['response'];
                }
            }
        }

        // Default response
        return "I'm here to help with lost and found items. You can:\n\n" .
               "1️⃣ Report a lost item\n" .
               "2️⃣ Report a found item\n" .
               "3️⃣ Check claim status\n" .
               "4️⃣ Get help\n\n" .
               "What would you like to do?";
    }

    protected function addSystemMessage($message)
    {
        $this->messages[] = [
            'sender' => 'bot',
            'text' => $message
        ];
        Session::put('chat_messages', $this->messages);
        $this->dispatch('messageSent');
    }

    public function clearChat()
    {
        $this->messages = [];
        Session::forget('chat_messages');
        $this->mount(); // This will add the welcome message
        $this->dispatch('messageSent');
    }

    public function render()
    {
        return view('livewire.chat-interfaces');
    }

    public function redirectTo($route)
    {
        return $this->redirect(route($route), navigate: true);
    }
}
