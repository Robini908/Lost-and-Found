<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class UpdateExtendedProfileInformation extends Component
{
    use WithFileUploads;

    public $state = [];
    public $social = [];

    public function mount()
    {
        $user = Auth::user();
        $this->state = [
            'phone_number' => $user->phone_number,
            'country_code' => $user->country_code,
            'address' => $user->address,
            'city' => $user->city,
            'state' => $user->state,
            'country' => $user->country,
            'postal_code' => $user->postal_code,
            'date_of_birth' => $user->date_of_birth,
            'gender' => $user->gender,
            'bio' => $user->bio,
            'occupation' => $user->occupation,
            'company' => $user->company,
            'website' => $user->website,
            'emergency_contact_name' => $user->emergency_contact_name,
            'emergency_contact_number' => $user->emergency_contact_number,
            'emergency_contact_relationship' => $user->emergency_contact_relationship,
            'id_type' => $user->id_type,
            'id_number' => $user->id_number,
        ];

        $this->social = $user->social_links ?? [
            'facebook' => '',
            'twitter' => '',
            'linkedin' => '',
            'instagram' => '',
        ];
    }

    public function updateProfileInformation()
    {
        $this->validate([
            'state.phone_number' => ['nullable', 'string', 'max:20'],
            'state.country_code' => ['nullable', 'string', 'max:5'],
            'state.address' => ['nullable', 'string', 'max:255'],
            'state.city' => ['nullable', 'string', 'max:100'],
            'state.state' => ['nullable', 'string', 'max:100'],
            'state.country' => ['nullable', 'string', 'max:100'],
            'state.postal_code' => ['nullable', 'string', 'max:20'],
            'state.date_of_birth' => ['nullable', 'date'],
            'state.gender' => ['nullable', 'in:male,female,other'],
            'state.bio' => ['nullable', 'string', 'max:500'],
            'state.occupation' => ['nullable', 'string', 'max:100'],
            'state.company' => ['nullable', 'string', 'max:100'],
            'state.website' => ['nullable', 'url', 'max:255'],
            'state.emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'state.emergency_contact_number' => ['nullable', 'string', 'max:20'],
            'state.emergency_contact_relationship' => ['nullable', 'string', 'max:50'],
            'state.id_type' => ['nullable', 'string', 'max:50'],
            'state.id_number' => ['nullable', 'string', 'max:50'],
            'social.facebook' => ['nullable', 'url'],
            'social.twitter' => ['nullable', 'url'],
            'social.linkedin' => ['nullable', 'url'],
            'social.instagram' => ['nullable', 'url'],
        ]);

        $user = Auth::user();

        // Update user with state data
        foreach ($this->state as $key => $value) {
            $user->$key = $value;
        }

        // Update social links
        $user->social_links = $this->social;

        $user->save();

        $this->dispatch('profile-updated');
    }

    public function render()
    {
        return view('livewire.profile.update-extended-profile-information');
    }
}
