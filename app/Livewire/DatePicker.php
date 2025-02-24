<?php

namespace App\Livewire;

use Livewire\Component;

class DatePicker extends Component
{
    public $date;

    public function render()
    {
        return view('livewire.date-picker');
    }
}
