<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use App\Models\Faq;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function howItWorks()
    {
        return view('pages.how-it-works');
    }

    public function successStories()
    {
        $successStories = LostItem::whereNotNull('matched_found_item_id')
            ->with(['user', 'category'])
            ->latest()
            ->take(6)
            ->get();

        return view('pages.success-stories', compact('successStories'));
    }

    public function faqs()
    {
        $faqs = Faq::orderBy('order')->get();
        return view('pages.faqs', compact('faqs'));
    }

    public function reportItem()
    {
        return view('pages.report-item');
    }
}
