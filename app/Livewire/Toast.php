<?php

namespace App\Livewire;

use Livewire\Component;

class Toast extends Component
{
    public $message = '';
    public $type = 'success';
    public $show = false;

    protected $listeners = ['showToast'];

    public function showToast($message, $type = 'success')
    {
        $this->message = $message;
        $this->type = $type;
        $this->show = true;

        $this->dispatch('toast-shown');
    }

    public function render()
    {
        return view('livewire.toast');
    }
}
