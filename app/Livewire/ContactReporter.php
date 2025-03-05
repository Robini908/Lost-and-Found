<?php

namespace App\Livewire;

use App\Models\LostItem;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewContactMessage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ContactReporter extends Component
{
    public $showModal = false;
    public $item;
    public $message = '';
    public $subject = '';
    public $contactMethod = 'in_app';
    public $isSubmitting = false;
    public $messageLength = 0;

    protected $rules = [
        'subject' => 'required|min:5|max:100',
        'message' => 'required|min:20|max:1000',
        'contactMethod' => 'required|in:in_app,email'
    ];

    protected $messages = [
        'subject.required' => 'Please provide a subject for your message.',
        'subject.min' => 'Subject should be at least 5 characters.',
        'subject.max' => 'Subject should not exceed 100 characters.',
        'message.required' => 'Please write your message to the reporter.',
        'message.min' => 'Message should be at least 20 characters to be meaningful.',
        'message.max' => 'Message should not exceed 1000 characters.'
    ];

    public function updatedMessage($value)
    {
        $this->messageLength = strlen($value);
        $this->validateOnly('message');
    }

    public function updatedSubject($value)
    {
        $this->validateOnly('subject');
    }

    public function mount(LostItem $item)
    {
        $this->item = $item;
        // Pre-fill subject based on item type
        $this->subject = "Regarding your " . strtolower($item->item_type) . " item: " . $item->title;
    }

    public function openModal()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($this->item->user_id === Auth::id()) {
            $this->dispatch('toast',
                type: 'error',
                title: 'Error',
                message: 'You cannot contact yourself.'
            );
            return;
        }

        if ($this->item->is_anonymous) {
            $this->dispatch('toast',
                type: 'error',
                title: 'Error',
                message: 'This item was posted anonymously. Contact is not available.'
            );
            return;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['message', 'isSubmitting']);
        $this->resetValidation();
    }

    public function sendMessage()
    {
        if ($this->isSubmitting) {
            return;
        }

        $this->isSubmitting = true;

        try {
            $this->validate();

            $message = Message::create([
                'from_user_id' => Auth::id(),
                'to_user_id' => $this->item->user_id,
                'subject' => $this->subject,
                'message' => $this->message,
                'item_id' => $this->item->id,
                'contact_method' => $this->contactMethod
            ]);

            // Notify the reporter
            $this->item->user->notify(new NewContactMessage($message));

            $this->dispatch('toast',
                type: 'success',
                title: 'Message Sent',
                message: 'Your message has been sent successfully.'
            );

            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('toast',
                type: 'error',
                title: 'Error',
                message: 'Failed to send message. Please try again.'
            );

            logger()->error('Contact Reporter Error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'item_id' => $this->item->id
            ]);
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function render()
    {
        return view('livewire.contact-reporter');
    }
}
