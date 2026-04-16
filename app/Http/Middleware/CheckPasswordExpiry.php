<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if password has expired
            if ($user->isPasswordExpired()) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Your password has expired. Please reset your password.')
                    ->with('redirect_to_password_reset', true);
            }
        }

        return $next($request);
    }
}
