<?php

namespace App\Livewire;

use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\LostItem;

class ShareItem extends Component
{
    public $itemId;
    public $showModal = false;
    public $activeTab = 'social';
    public $qrCode;
    public $shareUrl;
    public $shareTitle;
    public $embedCode;

    public function mount($itemId)
    {
        $this->itemId = $itemId;
        $this->generateShareData();
    }

    protected function generateShareData()
    {
        $item = $this->getItem();
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

    public function getItem()
    {
        return LostItem::findOrFail($this->itemId);
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

    public function downloadQrCode()
    {
        $filename = 'qr-code-' . $this->itemId . '.svg';
        $qrCodeSvg = base64_decode($this->qrCode);

        return response()->streamDownload(function () use ($qrCodeSvg) {
            echo $qrCodeSvg;
        }, $filename, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ]);
    }

    public function render()
    {
        return view('livewire.share-item');
    }
}
