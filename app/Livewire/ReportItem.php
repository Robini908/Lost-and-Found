<?php

namespace App\Livewire;

use App\Models\Report;
use Livewire\Component;
use App\Notifications\ItemReported;
use Illuminate\Support\Facades\Auth;

class ReportItem extends Component
{
    public $item;
    public $showModal = false;
    public $reason = '';
    public $description = '';
    public $reportTypes = [
        'inappropriate' => 'Inappropriate Content',
        'spam' => 'Spam or Misleading',
        'duplicate' => 'Duplicate Item',
        'fake' => 'Fake or Fraudulent',
        'wrong_category' => 'Wrong Category',
        'other' => 'Other'
    ];

    protected $rules = [
        'reason' => 'required|string|in:inappropriate,spam,duplicate,fake,wrong_category,other',
        'description' => 'required|string|min:20|max:1000'
    ];

    protected $messages = [
        'reason.required' => 'Please select a reason for reporting.',
        'reason.in' => 'Please select a valid reason for reporting.',
        'description.required' => 'Please provide a detailed description of the issue.',
        'description.min' => 'Please provide at least 20 characters in your description.',
        'description.max' => 'Description cannot exceed 1000 characters.'
    ];

    public function mount($item)
    {
        $this->item = $item;
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['reason', 'description']);
        $this->resetValidation();
    }

    public function submitReport()
    {
        $this->validate();

        try {
            $report = Report::create([
                'item_id' => $this->item->id,
                'reporter_id' => Auth::id(),
                'reason' => $this->reason,
                'description' => $this->description,
                'status' => 'pending'
            ]);

            // Notify moderators
            $moderators = \App\Models\User::role('moderator')->get();
            foreach ($moderators as $moderator) {
                $moderator->notify(new ItemReported($report));
            }

            $this->closeModal();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Thank you for your report. Our team will review it shortly.'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'There was an error submitting your report. Please try again.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.report-item');
    }
}
