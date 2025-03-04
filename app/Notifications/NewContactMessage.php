<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessage extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Message Regarding Your Item')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have received a new message regarding your item:')
            ->line('Subject: ' . $this->message->subject)
            ->line('From: ' . $this->message->fromUser->name)
            ->action('View Message', route('messages.show', $this->message->id))
            ->line('Thank you for using our platform!');
    }

    public function toArray($notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'from_user_id' => $this->message->from_user_id,
            'subject' => $this->message->subject,
            'item_id' => $this->message->item_id,
            'type' => 'new_message'
        ];
    }
}
