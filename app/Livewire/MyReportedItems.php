<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LostItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class MyReportedItems extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $isEditing = false;
    public $editItemId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function editItem($itemId)
    {
        $this->editItemId = $itemId;
        $this->isEditing = true;
    }

    #[On('closeEdit')]
    public function closeEdit()
    {
        $this->isEditing = false;
        $this->editItemId = null;
    }

    #[On('itemUpdated')]
    public function handleItemUpdated()
    {
        $this->closeEdit();
    }

    #[On('itemDeleted')]
    public function handleItemDeleted()
    {
        $this->closeEdit();
    }

    public function render()
    {
        $items = LostItem::where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.my-reported-items', [
            'items' => $items
        ]);
    }
}
