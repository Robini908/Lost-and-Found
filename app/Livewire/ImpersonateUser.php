<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Lab404\Impersonate\Services\ImpersonateManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class ImpersonateUser extends Component
{
    use WithPagination;

    #[Rule('nullable|string|min:3')]
    public string $search = '';

    public bool $isImpersonating = false;
    public bool $showModal = false;

    protected $queryString = ['search'];

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount(): void
    {
        if (!Auth::check() || !Auth::user()?->canImpersonate()) {
            abort(403, 'Unauthorized action.');
        }

        $this->isImpersonating = app(ImpersonateManager::class)->isImpersonating();
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->search = '';
        $this->resetPage();
    }

    public function impersonate(int $userId): mixed
    {
        try {
            $user = User::findOrFail($userId);
            $currentUser = Auth::user();

            if (!$currentUser || !$currentUser->canImpersonate()) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'You are not authorized to impersonate users.'
                ]);
                return null;
            }

            if ($user->isImpersonated()) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'This user is already being impersonated.'
                ]);
                return null;
            }

            // Store the current session ID before impersonating
            $originalSession = Session::getId();
            Session::put('impersonator_session', $originalSession);

            // Take impersonation
            app(ImpersonateManager::class)->take(
                $currentUser,
                $user,
                config('impersonate.guard', 'sanctum')
            );

            $this->closeModal();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Successfully impersonating ' . $user->name
            ]);

            return redirect()->to(config('impersonate.take_redirect_to', '/dashboard'));
        } catch (\Exception $e) {
            report($e);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to impersonate user. Please try again.'
            ]);
            return null;
        }
    }

    public function stopImpersonating(): mixed
    {
        if (!app(ImpersonateManager::class)->isImpersonating()) {
            return null;
        }

        try {
            // Get the original session ID
            $originalSession = Session::get('impersonator_session');

            app(ImpersonateManager::class)->leave();

            // Restore the original session if available
            if ($originalSession) {
                Session::setId($originalSession);
                Session::start();
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Stopped impersonating user.'
            ]);

            $this->closeModal();
            return redirect()->to(config('impersonate.leave_redirect_to', '/dashboard'));
        } catch (\Exception $e) {
            report($e);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to stop impersonating. Please try again.'
            ]);
            return null;
        }
    }

    #[Computed]
    public function users(): mixed
    {
        return User::where('id', '!=', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(5);
    }

    public function render()
    {
        return view('livewire.impersonate-user', [
            'users' => $this->users
        ]);
    }
}
