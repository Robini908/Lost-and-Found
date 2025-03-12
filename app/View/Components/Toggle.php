<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Toggle extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $id = null,
        public bool $disabled = false
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.toggle');
    }
} 