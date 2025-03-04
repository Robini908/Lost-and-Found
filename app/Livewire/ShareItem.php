<?php

namespace App\Livewire;

use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ShareItem extends Component
{
    public $item;
    public $showModal = false;
    public $activeTab = 'social';
    public $qrCode;
    public $shareUrl;
    public $shareTitle;
    public $embedCode;

    public function mount($item)
    {
        $this->item = $item;
        $this->shareUrl = route('lost-items.show', $item);
        $this->shareTitle = $item->title;

        // Generate QR code
        $this->qrCode = base64_encode(QrCode::format('svg')
            ->size(200)
            ->style('round')
            ->eye('circle')
            ->color(37, 99, 235) // Blue-600
            ->generate($this->shareUrl));

        // Generate embed code
        $this->embedCode = '<iframe src="' . $this->shareUrl . '/embed" width="100%" height="400" frameborder="0"></iframe>';
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function copyToClipboard($text)
    {
        $this->dispatch('clipboard-copy', ['text' => $text]);
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Copied to clipboard successfully!'
        ]);
    }

    public function render()
    {
        return view('livewire.share-item');
    }
}
