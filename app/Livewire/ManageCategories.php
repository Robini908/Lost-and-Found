<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;

class ManageCategories extends Component
{
    use WithPagination;

    public $name;
    public $editCategoryId = null;
    public $editName;

    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name',
    ];

    public function render()
    {
        $categories = Category::paginate(10);
        return view('livewire.manage-categories', compact('categories'));
    }

    public function addCategory()
    {
        $this->validate();

        Category::create(['name' => $this->name]);

        $this->reset('name');
        session()->flash('message', 'Category added successfully!');
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        $this->editCategoryId = $id;
        $this->editName = $category->name;
    }

    public function updateCategory()
    {
        $this->validate([
            'editName' => 'required|string|max:255|unique:categories,name,' . $this->editCategoryId,
        ]);

        $category = Category::findOrFail($this->editCategoryId);
        $category->update(['name' => $this->editName]);

        $this->reset('editCategoryId', 'editName');
        session()->flash('message', 'Category updated successfully!');
    }

    public function deleteCategory($id)
    {
        Category::findOrFail($id)->delete();
        session()->flash('message', 'Category deleted successfully!');
    }

    public function resetEdit()
    {
        $this->reset('editCategoryId', 'editName');
    }
}
