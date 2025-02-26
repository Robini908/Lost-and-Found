<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LostItem;
use App\Services\ItemMatchingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ItemMatchingController extends Controller
{
    protected $itemMatchingService;

    public function __construct(ItemMatchingService $itemMatchingService)
    {
        $this->itemMatchingService = $itemMatchingService;
    }

    public function index()
    {
        $user = Auth::user();
        $unmatchedItems = LostItem::where('user_id', $user->id)
            ->whereIn('item_type', ['reported', 'searched'])
            ->with('images')
            ->get();

        return view('match-lost-and-found-items', [
            'unmatchedItems' => $unmatchedItems,
        ]);
    }

    public function findMatches(Request $request)
    {
        Session::flash('loading', true);
        Session::flash('loadingMessage', 'Initiating matching process...');
        Session::flash('progress', 0);

        $user = Auth::user();
        $reportedItems = LostItem::where('user_id', $user->id)
            ->whereIn('item_type', ['reported', 'searched'])
            ->with('images')
            ->get();

        $foundItems = LostItem::where('item_type', 'found')
            ->with('images')
            ->get();

        $matches = $this->itemMatchingService->findMatches($reportedItems, $foundItems);

        if (count($matches) > 0) {
            Session::flash('bannerMessage', 'We found potential matches!');
        } else {
            Session::flash('bannerMessage', 'No matches found.');
        }

        Session::flash('loading', false);

        return redirect()->back()->with('matches', $matches);
    }
}
