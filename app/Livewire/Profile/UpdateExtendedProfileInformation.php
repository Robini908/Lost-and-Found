<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class UpdateExtendedProfileInformation extends Component
{
    use WithFileUploads;

    public $state = [];
    public $social = [];
    public $location_type = 'map'; // Default to map type

    public function mount()
    {
        $user = Auth::user();
        $this->state = [
            'phone_number' => $user->phone_number,
            'country_code' => $user->country_code,
            'location_type' => $user->location_type ?? 'map',
            'location_address' => $user->location_address,
            'area' => $user->area,
            'landmarks' => $user->landmarks,
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
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

        $this->location_type = $user->location_type ?? 'map';
    }

    public function updateProfileInformation()
    {
        $this->validate([
            'state.phone_number' => ['nullable', 'string', 'max:20'],
            'state.country_code' => ['nullable', 'string', 'max:5'],
            'state.location_type' => ['required', 'string', 'in:map,area'],
            'state.location_address' => ['required_if:state.location_type,map', 'nullable', 'string', 'max:255'],
            'state.area' => ['required_if:state.location_type,area', 'nullable', 'string', 'max:255'],
            'state.landmarks' => ['nullable', 'string', 'max:255'],
            'state.latitude' => ['required_if:state.location_type,map', 'nullable', 'numeric'],
            'state.longitude' => ['required_if:state.location_type,map', 'nullable', 'numeric'],
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

        /** @var User $user */
        $user = Auth::user();

        // Update user with state data
        foreach ($this->state as $key => $value) {
            if ($key === 'location_type') {
                // If location type is area, clear map-related fields
                if ($value === 'area') {
                    $user->location_address = null;
                    $user->latitude = null;
                    $user->longitude = null;
                } else {
                    // If location type is map, clear area-related fields
                    $user->area = null;
                }
            }
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
