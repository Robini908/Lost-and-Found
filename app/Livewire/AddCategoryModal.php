<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;

class AddCategoryModal extends Component
{
    public $name;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name',
    ];

    public function saveCategory()
    {
        $this->validate();

        Category::create(['name' => $this->name]);

        $this->dispatch('categoryAdded'); // Emit an event to notify the parent component

        $this->reset('name');
        $this->showModal = false;
        session()->flash('message', 'Category added successfully!');
    }

    public function render()
    {
        return view('livewire.add-category-modal');
    }
}
