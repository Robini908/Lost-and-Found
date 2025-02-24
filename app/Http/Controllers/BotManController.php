<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use BotMan\BotMan\Messages\Incoming\Answer;

class BotManController extends Controller
{
    public function handle()
    {
        $botman = app('botman');

        $botman->hears('hello', function (BotMan $bot) {
            $bot->reply('Hello! How can I assist you today?');
        });

        $botman->listen();
    }
}
