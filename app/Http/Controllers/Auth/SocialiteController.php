<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Team; // Import the Team model
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // Check if the user already exists
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // Create a new user with the full name
                $user = User::create([
                    'name' => $socialUser->getName(), // Capture the full name
                    'email' => $socialUser->getEmail(),
                    'password' => bcrypt(Str::random(16)), // Generate a random password
                    'email_verified_at' => now(), // Mark the email as verified
                ]);

                // Create a default team for the user
                $user->ownedTeams()->save(Team::forceCreate([
                    'user_id' => $user->id,
                    'name' => explode(' ', $user->name, 2)[0] . "'s Team", // Example: "John's Team"
                    'personal_team' => true,
                ]));

                // Notify the user that their account has been created
                toast()
                    ->success('Your account has been created successfully!')
                    ->pushOnNextPage();
            }

            // Log the user in
            Auth::login($user, true);

            // Debugging: Check if the user is authenticated
            if (Auth::check()) {
                // Notify the user that they have logged in
                toast()
                    ->success('You have successfully logged in!')
                    ->push();
            } else {
                Log::error('User not authenticated.');
            }

            return redirect()->route('dashboard'); // Redirect to the dashboard after login
        } catch (\Exception $e) {
            Log::error('Socialite login error: ' . $e->getMessage());

            // Notify the user of the error
            toast()
                ->danger('Unable to login with ' . ucfirst($provider) . '. Please try again.')
                ->push();

            return redirect()->route('login')->withErrors(['error' => 'Unable to login with ' . ucfirst($provider) . '. Please try again.']);
        }
    }
}
