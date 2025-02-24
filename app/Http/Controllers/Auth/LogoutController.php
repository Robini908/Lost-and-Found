<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            toast()
                ->success('You have been logged out successfully.')
                ->push();

            return redirect('/');
        } catch (Exception $e) {
            toast()
                ->danger('An error occurred while logging out. Please try again.')
                ->push();

            return redirect()->back();
        }
    }
}
