<?php

namespace App\Http\Controllers\Agents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Agent;
use Illuminate\Validation\ValidationException;

class AgentAuthController extends Controller
{
    /**
     * Show agent login form
     */
    public function showLogin()
    {
        return view('agents.auth.login');
    }

    /**
     * Handle agent login
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // Can be phone or email
            'password' => 'required|string',
            'remember' => 'boolean',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // First, try to find the agent by phone or email
        $agent = Agent::where('phone', $login)
                     ->orWhere('email', $login)
                     ->first();
        
        if (!$agent) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if agent is active
        if (!$agent->isActive()) {
            $statusMessage = match($agent->status) {
                'inactive' => 'Your account is currently inactive. Please contact support.',
                'suspended' => 'Your account has been suspended. Please contact support.',
                default => 'Your account is not active. Please contact support.'
            };
            
            throw ValidationException::withMessages([
                'login' => [$statusMessage],
            ]);
        }

        // Check password manually since we found the agent by phone/email
        if (!Hash::check($password, $agent->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        // Manually log in the agent
        Auth::guard('agent')->login($agent, $remember);
        
        // Update last login information
        $agent->updateLastLogin();
        
        $request->session()->regenerate();

        return redirect()->intended(route('agent.root'));
    }

    /**
     * Handle agent logout
     */
    public function logout(Request $request)
    {
        Auth::guard('agent')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('agent.login');
    }


}
