<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LostItem;
use App\Models\PotentialMatch;
use Illuminate\Support\Facades\Auth;

class NotificationCounter extends Component
{
    public $notificationCount = 0;

    protected $listeners = ['refreshNotifications' => 'getNotificationCount'];

    public function mount()
    {
        $this->getNotificationCount();
    }

    public function getNotificationCount()
    {
        $userId = Auth::id();

        // Count unviewed matches for user's items
        $unviewedMatchesCount = PotentialMatch::where('viewed', false)
            ->whereHas('lostItem', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orWhereHas('foundItem', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();

        // Count new items in the last 7 days (excluding user's own items)
        $newItemsCount = LostItem::where('created_at', '>=', now()->subDays(7))
            ->where('status', 'pending')
            ->where('user_id', '!=', $userId)
            ->count();

        $this->notificationCount = $unviewedMatchesCount + $newItemsCount;
    }

    public function render()
    {
        return view('livewire.notification-counter');
    }
}
