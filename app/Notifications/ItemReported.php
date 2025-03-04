<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemReported extends Notification implements ShouldQueue
{
    use Queueable;

    protected $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Item Report: Action Required')
            ->greeting('Hello ' . $notifiable->name)
            ->line('A new item has been reported and requires your attention.')
            ->line('Item: ' . $this->report->item->title)
            ->line('Reason: ' . ucfirst($this->report->reason))
            ->line('Reporter: ' . $this->report->reporter->name)
            ->action('Review Report', route('admin.reports.show', $this->report))
            ->line('Please review this report as soon as possible to maintain community standards.');
    }

    public function toArray($notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'item_id' => $this->report->item_id,
            'reporter_id' => $this->report->reporter_id,
            'reason' => $this->report->reason,
            'type' => 'item_reported'
        ];
    }
}
