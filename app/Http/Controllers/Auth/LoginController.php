<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            $user->unlockIfExpired();

            if ($user->account_status !== 'active') {
                $user->logActivity('login_blocked', 'warning', [
                    'reason' => 'account_status_' . $user->account_status,
                ]);

                return back()->withErrors([
                    'email' => 'Your account is not active. Contact an administrator for assistance.',
                ])->onlyInput('email');
            }

            if ($user->isLockedOut()) {
                $user->logActivity('login_attempt_locked', 'warning');

                return back()->withErrors([
                    'email' => 'Your account is temporarily locked due to repeated failed login attempts. Please try again later.',
                ])->onlyInput('email');
            }
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Get logged in user
            $user = Auth::user();
            
            // Reset failed login counters and record login
            $user->resetFailedLoginAttempts();
            $user->recordLogin();
            
            // Redirect to dashboard
            return redirect()->intended('dashboard');
        }

        if ($user) {
            $user->recordFailedLoginAttempt();
        }

        // If login fails
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Log the logout if user is logged in
        if (Auth::check()) {
            Auth::user()->logActivity('logout', 'success');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}