<?php

namespace App\Livewire;

use Livewire\Component;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\BotMan;

class Chat extends Component
{
    public $messages = [];
    public $message;

    public function sendMessage()
    {
        $this->messages[] = ['user' => 'You', 'text' => $this->message];

        // BotMan configuration
        $config = [];
        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
        $botman = BotManFactory::create($config);

        $botman->hears($this->message, function (BotMan $bot) {
            $reply = $bot->getMessage()->getText();
            $this->messages[] = ['user' => 'Bot', 'text' => $reply];
        });

        $botman->listen();

        $this->message = '';
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
