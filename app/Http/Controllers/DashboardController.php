<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use App\Models\Activity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch recent items with their relationships
        $recentItems = LostItem::with(['images', 'category', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get()
            ->map(function ($item) {
                // Add location if available
                $item->location = $item->location_address ?? $item->area ?? $item->location_lost ?? $item->location_found;
                return $item;
            });

        // Fetch recent activities
        $recentActivities = Activity::where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact('recentItems', 'recentActivities'));
    }
}
