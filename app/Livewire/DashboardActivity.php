<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use Carbon\Carbon;

class DashboardActivity extends Component
{
    public $activities = [];
    public $limit = 5;

    public function mount()
    {
        $this->loadActivities();
    }

    public function loadActivities()
    {
        $this->activities = Activity::with(['user'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit($this->limit)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'created_at' => $activity->created_at,
                    'link' => $activity->link,
                    'user' => [
                        'name' => $activity->user->name,
                        'avatar' => $activity->user->profile_photo_url
                    ]
                ];
            });
    }

    public function getListeners()
    {
        return [
            'echo:activities,ActivityCreated' => 'loadActivities',
            'refresh-activities' => 'loadActivities'
        ];
    }

    public function render()
    {
        return view('livewire.dashboard-activity');
    }
}
