<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Usernotnull\Toast\Concerns\WireToast;
use Exception;
use Livewire\WithPagination;

class ImpersonateUser extends Component
{
    use WireToast, WithPagination;

    public $search = '';
    public $selectedUserId = null;
    public $filterRole = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterRole' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function startImpersonation()
    {
        try {
            if (!auth()->user()->hasRole('superadmin')) {
                toast()->danger('Unauthorized action.')->push();
                return;
            }

            if (!$this->selectedUserId) {
                toast()->warning('Please select a user to impersonate.')->push();
                return;
            }

            $userToImpersonate = User::findOrFail($this->selectedUserId);

            // Store current user's ID and remember URL in session
            Session::put('impersonator_id', Auth::id());
            Session::put('impersonator_remember_url', url()->previous());

            // Login as the impersonated user
            Auth::login($userToImpersonate);

            toast()->success("Now impersonating {$userToImpersonate->name}")->pushOnNextPage();
            return redirect()->route('dashboard');

        } catch (Exception $e) {
            logger()->error('Impersonation error: ' . $e->getMessage());
            toast()->danger('Failed to start impersonation.')->push();
        }
    }

    public function stopImpersonation()
    {
        try {
            $impersonatorId = Session::get('impersonator_id');
            $rememberUrl = Session::get('impersonator_remember_url');

            if (!$impersonatorId) {
                toast()->danger('No impersonation session found.')->push();
                return;
            }

            $originalUser = User::findOrFail($impersonatorId);

            // Clear impersonation session data
            Session::forget(['impersonator_id', 'impersonator_remember_url']);

            // Log back in as original user
            Auth::login($originalUser);

            toast()->success('Impersonation ended.')->pushOnNextPage();
            return redirect($rememberUrl ?: route('dashboard'));

        } catch (Exception $e) {
            logger()->error('Stop impersonation error: ' . $e->getMessage());
            toast()->danger('Failed to stop impersonation.')->push();
        }
    }

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field
            ? $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc'
            : 'asc';

        $this->sortField = $field;
    }

    public function render()
    {
        $users = User::query()
            ->where('id', '!=', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterRole, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', $this->filterRole);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $roles = \Spatie\Permission\Models\Role::pluck('name');

        return view('livewire.impersonate-user', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}
