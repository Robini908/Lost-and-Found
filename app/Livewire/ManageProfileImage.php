<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\LivewireFilepond\WithFilePond;
use Usernotnull\Toast\Concerns\WireToast;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Exception;

class ManageProfileImage extends Component
{
    use WithFileUploads, WireToast, WithFilePond;

    public $profileImage;

    protected $rules = [
        'profileImage' => 'image|max:1024', // 1MB Max
    ];

    public function saveProfileImage()
    {
        try {
            // Debug the uploaded file
            if ($this->profileImage) {
                Log::info('Uploaded file:', [
                    'name' => $this->profileImage->getClientOriginalName(),
                    'size' => $this->profileImage->getSize(),
                    'mime' => $this->profileImage->getMimeType(),
                ]);
            } else {
                Log::error('No file uploaded or $profileImage is null.');
                throw new Exception('No file uploaded or $profileImage is null.');
            }

            // Validate the uploaded image
            $this->validate();

            // Get the authenticated user
            $user = Auth::user();

            // Store the uploaded image
            $imagePath = $this->profileImage->store('profile-photos', 'public');

            // Optimize the image
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize(storage_path("app/public/{$imagePath}"));

            // Delete the old profile photo if it exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Update the user's profile photo path
            $user->profile_photo_path = $imagePath;
            $user->save(); // Save the user model

            // Show a success message
            toast()->success('Profile image updated successfully.')->push();

            // Reset the file input
            $this->reset('profileImage');

            // Emit an event to refresh the component
            $this->dispatch('profileUpdated');
        } catch (ValidationException $e) {
            Log::error('Validation failed:', ['errors' => $e->errors()]);
            toast()->danger('Validation failed. Please upload a valid image.')->push();
        } catch (Exception $e) {
            Log::error('An error occurred while saving the profile image:', ['message' => $e->getMessage()]);
            toast()->danger('An error occurred while saving the profile image. Please try again.')->push();
        }
    }

    public function deleteProfileImage()
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Delete the profile photo if it exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
                $user->profile_photo_path = null;
                $user->save(); // Save the user model

                // Show a success message
                toast()->success('Profile image deleted successfully.')->push();

                // Emit an event to refresh the component
                $this->dispatch(event: 'profileUpdated');
            } else {
                throw new Exception('No profile image to delete.');
            }
        } catch (Exception $e) {
            Log::error('An error occurred while deleting the profile image:', ['message' => $e->getMessage()]);
            toast()->danger('An error occurred while deleting the profile image. Please try again.')->push();
        }
    }

    public function render()
    {
        return view('livewire.manage-profile-image');
    }
}
