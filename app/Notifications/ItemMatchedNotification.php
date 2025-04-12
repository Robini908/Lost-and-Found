<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\LostItem;
use App\Models\ItemMatch;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ItemMatchedNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $match;
    protected $userRole; // 'finder' or 'reporter'

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array
     */
    public $backoff = [60, 180, 360]; // 1 minute, 3 minutes, 6 minutes

    /**
     * Determine which queues should be used for each notification channel.
     *
     * @var array
     */
    public $viaQueues = [
        'mail' => 'notifications-mail',
        'database' => 'notifications-database'
    ];

    /**
     * Create a new notification instance.
     */
    public function __construct(ItemMatch $match, string $userRole)
    {
        $this->match = $match;
        $this->userRole = $userRole;
        $this->afterCommit = true; // Only dispatch notification after database transaction commits
        $this->onQueue('notifications'); // Default queue if not specified in viaQueues
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $lostItem = $this->match->lostItem;
        $foundItem = $this->match->foundItem;
        $similarityScore = round($this->match->similarity_score * 100, 1);

        if ($this->userRole === 'reporter') {
            return (new MailMessage)
                ->subject('Potential Match Found for Your Lost Item!')
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('Great news! We\'ve found a potential match for your lost item.')
                ->line("Your item: {$lostItem->title}")
                ->line("Match confidence: {$similarityScore}%")
                ->line('A similar item has been found with these details:')
                ->line("- Title: {$foundItem->title}")
                ->line("- Location: {$foundItem->location}")
                ->line("- Date Found: {$foundItem->date_found->format('F j, Y')}")
                ->action('View Match Details', route('lost-items.show', $foundItem))
                ->line('Please review the match and contact the finder if this appears to be your item.');
        } else {
            return (new MailMessage)
                ->subject('Your Found Item Has a Potential Match!')
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('We\'ve found a potential match for an item you reported as found.')
                ->line("Your found item: {$foundItem->title}")
                ->line("Match confidence: {$similarityScore}%")
                ->line('This item matches a lost item report with these details:')
                ->line("- Title: {$lostItem->title}")
                ->line("- Location: {$lostItem->location}")
                ->line("- Date Lost: {$lostItem->date_lost->format('F j, Y')}")
                ->action('View Match Details', route('lost-items.show', $lostItem))
                ->line('The owner has been notified and may contact you soon.');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'match_id' => $this->match->id,
            'lost_item_id' => $this->match->lost_item_id,
            'found_item_id' => $this->match->found_item_id,
            'similarity_score' => $this->match->similarity_score,
            'user_role' => $this->userRole,
        ];
    }

    /**
     * Handle a notification failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Failed to send item match notification', [
            'match_id' => $this->match->id,
            'user_role' => $this->userRole,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
