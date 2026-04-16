<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show user profile page
     */
    public function show()
    {
        $user = Auth::user();
        $user->load('institution', 'department', 'roles');
        
        return view('profile.show', compact('user'));
    }

    /**
     * Show change password page
     */
    public function editPassword()
    {
        return view('profile.change-password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $passwordErrorMessage = 'password requires uppercase, number, special character (@$!%*?&) and minimum 8 characters';
        
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'min:8',
                'confirmed',
                function ($attribute, $value, $fail) use ($passwordErrorMessage) {
                    if (!\App\Models\User::validatePasswordComplexity($value)) {
                        $fail($passwordErrorMessage);
                    }
                },
            ],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => now(),
            'last_password_change_at' => now(),
            'password_expires_at' => now()->addDays(90), // Reset expiry to 90 days from now
        ]);

        $user->logActivity('password_change', 'success', 'User changed their password');

        return redirect()->route('profile.show')
            ->with('success', 'Password changed successfully!');
    }

    /**
     * Show edit profile page
     */
    public function edit()
    {
        $user = Auth::user();
        $user->load('institution', 'department');

        return view('profile.edit', compact('user'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone_mobile' => ['nullable', 'string', 'max:20'],
            'phone_work' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);
        $user->logActivity('profile_update', 'success', 'User updated their profile');

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully!');
    }
}
