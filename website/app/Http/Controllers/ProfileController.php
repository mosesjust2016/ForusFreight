<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('client.profile.index', [
            'user' => Auth::user(),
        ]);
    }

    public function security()
    {
        return view('client.profile.security');
    }

    public function settings()
    {
        return view('client.settings');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile information has been updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Password is NOT hashed here — the 'hashed' cast on the User model
        // handles hashing automatically when the attribute is set.
        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return back()->with('status', 'password-updated');
    }

    public function help()
    {
        return view('client.help');
    }
}
