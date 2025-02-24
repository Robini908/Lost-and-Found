<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Usernotnull\Toast\Concerns\WireToast;
use Exception;

class ImpersonateUser extends Component
{
    use WireToast;

    public $search = ''; // For searching users
    public $selectedUserId = null; // Track the selected user

    // Start impersonation
    public function startImpersonation()
    {
        try {
            // Validate if a user is selected
            if (!$this->selectedUserId) {
                toast()->warning('Please select a user to impersonate.')->push();
                return;
            }

            // Find the user to impersonate
            $user = User::where('id', $this->selectedUserId)->firstOrFail();

            if (!$user) {
                toast()->danger('User not found.')->push();
                return;
            }

            // Store the original user ID in the session
            Session::put('impersonator_id', Auth::id());

            // Log in as the impersonated user
            Auth::login($user);

            // Redirect to the dashboard of the impersonated user
            toast()->success("You are now impersonating {$user->name}.")->pushOnNextPage();
            return redirect()->route('dashboard');
        } catch (Exception $e) {
            // Log the error for debugging
            logger()->error('Impersonation error: ' . $e->getMessage());

            // Display a user-friendly error message
            toast()->danger('An error occurred while starting impersonation. Please try again.')->push();
        }
    }

    // Stop impersonation
    public function stopImpersonation()
    {
        try {
            // Retrieve the original user ID from the session
            $impersonatorId = Session::pull('impersonator_id');

            if (!$impersonatorId) {
                toast()->danger('Unable to stop impersonation: No impersonator ID found.')->push();
                return;
            }

            // Find the original user
            $impersonator = User::where('id', $impersonatorId)->firstOrFail();

            if (!$impersonator) {
                toast()->danger('Unable to stop impersonation: Original user not found.')->push();
                return;
            }

            // Log back in as the original user
            Auth::login($impersonator);

            // Redirect to the dashboard of the original user
            toast()->success('You have stopped impersonating the user.')->pushOnNextPage();
            return redirect()->route('dashboard');
        } catch (Exception $e) {
            // Log the error for debugging
            logger()->error('Stop impersonation error: ' . $e->getMessage());

            // Display a user-friendly error message
            toast()->danger('An error occurred while stopping impersonation. Please try again.')->push();
        }
    }

    // Check if the current user is impersonating another user
    public function isImpersonating()
    {
        return Session::has('impersonator_id');
    }

    // Select a user
    public function selectUser($userId)
    {
        $this->selectedUserId = $userId;
    }

    public function render()
    {
        try {
            // Filter users based on the search term
            $users = User::query()
                ->where('id', '!=', Auth::id()) // Exclude the current user
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                          ->orWhere('email', 'like', "%{$this->search}%");
                    });
                })
                ->get();

            return view('livewire.impersonate-user', [
                'users' => $users,
            ]);
        } catch (Exception $e) {
            // Log the error for debugging
            logger()->error('Render error: ' . $e->getMessage());

            // Display a user-friendly error message
            toast()->danger('An error occurred while loading the user list. Please try again.')->push();

            return view('livewire.impersonate-user', [
                'users' => collect(), // Return an empty collection to prevent breaking the UI
            ]);
        }
    }
}
