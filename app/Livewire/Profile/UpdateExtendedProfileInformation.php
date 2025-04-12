<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Usernotnull\Toast\Concerns\WireToast;

class UpdateExtendedProfileInformation extends Component
{
    use WithFileUploads;
    use WireToast;

    public $state = [];
    public $social = [];
    public $location_type = 'map'; // Default to map type

    protected $listeners = ['profile-updated' => '$refresh'];

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

        // Log initial state for debugging
        Log::info('Profile component mounted', [
            'user_id' => $user->id,
            'state' => $this->state,
        ]);
    }

    public function updateProfileInformation()
    {
        $this->validate([
            'state.phone_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\+\-\(\)\s]*$/'],
            'state.country_code' => ['nullable', 'string', 'max:5', 'regex:/^[\+0-9]*$/'],
            'state.location_type' => ['required', 'string', 'in:map,area'],
            'state.location_address' => ['required_if:state.location_type,map', 'nullable', 'string', 'max:255'],
            'state.area' => ['required_if:state.location_type,area', 'nullable', 'string', 'max:255'],
            'state.landmarks' => ['nullable', 'string', 'max:255'],
            'state.latitude' => ['required_if:state.location_type,map', 'nullable', 'numeric', 'between:-90,90'],
            'state.longitude' => ['required_if:state.location_type,map', 'nullable', 'numeric', 'between:-180,180'],
            'state.city' => ['nullable', 'string', 'max:100'],
            'state.state' => ['nullable', 'string', 'max:100'],
            'state.country' => ['nullable', 'string', 'max:100'],
            'state.postal_code' => ['nullable', 'string', 'max:20', 'regex:/^[a-zA-Z0-9\-\s]*$/'],
            'state.date_of_birth' => ['nullable', 'date', 'before:today'],
            'state.gender' => ['nullable', 'in:male,female,other'],
            'state.bio' => ['nullable', 'string', 'max:500'],
            'state.occupation' => ['nullable', 'string', 'max:100'],
            'state.company' => ['nullable', 'string', 'max:100'],
            'state.website' => ['nullable', 'url', 'max:255'],
            'state.emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'state.emergency_contact_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\+\-\(\)\s]*$/'],
            'state.emergency_contact_relationship' => ['nullable', 'string', 'max:50'],
            'state.id_type' => ['nullable', 'string', 'max:50'],
            'state.id_number' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z0-9\-\s]*$/'],
            'social.facebook' => ['nullable', 'url'],
            'social.twitter' => ['nullable', 'url'],
            'social.linkedin' => ['nullable', 'url'],
            'social.instagram' => ['nullable', 'url'],
        ]);

        try {
            /** @var User $user */
            $user = Auth::user();

            // Log before sanitization
            Log::info('Before sanitization', [
                'user_id' => $user->id,
                'state' => $this->state,
            ]);

            // Sanitize inputs before saving
            foreach ($this->state as $key => $value) {
                if (is_string($value)) {
                    $this->state[$key] = $this->sanitizeInput($value);
                }
            }

            foreach ($this->social as $key => $value) {
                if (is_string($value)) {
                    $this->social[$key] = $this->sanitizeInput($value);
                }
            }

            // Log after sanitization
            Log::info('After sanitization', [
                'user_id' => $user->id,
                'state' => $this->state,
            ]);

            // Use database transaction to ensure data integrity
            DB::beginTransaction();

            try {
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

                    // Make sure the field exists on the user model
                    if (array_key_exists($key, $user->getAttributes()) || in_array($key, $user->getFillable())) {
                        $user->$key = $value;
                    } else {
                        Log::warning('Attempted to set non-existent field', [
                            'user_id' => $user->id,
                            'field' => $key,
                            'value' => $value
                        ]);
                    }
                }

                // Update social links
                $user->social_links = $this->social;

                // Try direct update if model save doesn't work
                try {
                    // Save the user and check if it was successful
                    $saved = $user->save();

                    if (!$saved) {
                        // Try direct database update as fallback
                        $updatedRows = DB::table('users')
                            ->where('id', $user->id)
                            ->update([
                                'phone_number' => $user->phone_number,
                                'country_code' => $user->country_code,
                                'location_type' => $user->location_type,
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
                                'social_links' => json_encode($this->social),
                                'emergency_contact_name' => $user->emergency_contact_name,
                                'emergency_contact_number' => $user->emergency_contact_number,
                                'emergency_contact_relationship' => $user->emergency_contact_relationship,
                                'id_type' => $user->id_type,
                                'id_number' => $user->id_number,
                                'updated_at' => now(),
                            ]);

                        if ($updatedRows === 0) {
                            DB::rollBack();
                            Log::error('Failed to update user profile via direct DB update', [
                                'user_id' => $user->id
                            ]);
                            toast()->danger('Failed to update profile. Please try again.')->push();
                            return;
                        }

                        Log::info('Profile updated successfully via direct DB update', [
                            'user_id' => $user->id,
                            'rows_updated' => $updatedRows
                        ]);
                    } else {
                        Log::info('Profile updated successfully via model save', [
                            'user_id' => $user->id,
                            'updated_data' => $user->getDirty()
                        ]);
                    }

                    DB::commit();
                    toast()->success('Profile updated successfully!')->push();
                    $this->dispatch('profile-updated');

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Exception during save operation', [
                        'user_id' => $user->id,
                        'exception' => $e->getMessage()
                    ]);
                    toast()->danger('Error saving profile: ' . $e->getMessage())->push();
                }

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Exception while updating profile', [
                'user_id' => Auth::id(),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            toast()->danger('An error occurred: ' . $e->getMessage())->push();
        }
    }

    /**
     * Sanitize input to prevent XSS attacks
     */
    protected function sanitizeInput($input)
    {
        // Remove potentially harmful HTML tags
        $sanitized = strip_tags($input);

        // Trim whitespace
        $sanitized = trim($sanitized);

        // Convert special characters to HTML entities
        return htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
    }

    public function render()
    {
        return view('livewire.profile.update-extended-profile-information');
    }
}
