<?php

namespace App\Livewire;

use Livewire\Component;

class Tour extends Component
{
    public $steps = [
        [
            'element' => '#step1',
            'content' => 'This is the first step.',
        ],
        [
            'element' => '#step2',
            'content' => 'This is the second step.',
        ],
        // Add more steps as needed
    ];

    public $currentStep = 0;

    public function nextStep()
    {
        if ($this->currentStep < count($this->steps) - 1) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 0) {
            $this->currentStep--;
        }
    }

    public function render()
    {
        return view('livewire.tour');
    }
}
