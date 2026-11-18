<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Public sign-up. Only available when AUTH_MODE=self_register.
 * In the default admin_managed mode these routes answer 404.
 */
class RegisterController extends Controller
{
    public function create(): View
    {
        $this->ensureSelfRegistrationIsEnabled();

        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $this->ensureSelfRegistrationIsEnabled();

        // Role is never taken from the request: public sign-ups are always regular users.
        $user = User::create($request->safe()->only(['name', 'school_id', 'email', 'password']));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Welcome! Your account has been created.');
    }

    private function ensureSelfRegistrationIsEnabled(): void
    {
        abort_unless(config('campus.auth_mode') === 'self_register', 404);
    }
}
