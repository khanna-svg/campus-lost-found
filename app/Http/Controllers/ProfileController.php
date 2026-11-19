<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user() ?: auth()->user();
        if (! $user) {
            abort(403);
        }

        return view('profile.show', ['user' => $user]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user() ?: auth()->user();
        if (! $user) {
            abort(403);
        }

        $user->update($request->validated());

        return redirect()->route('profile.show')->with('success', 'Profile updated.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user() ?: auth()->user();
        if (! $user) {
            abort(403);
        }

        $user->update(['password' => $request->validated('password')]);
        $user->forceFill(['must_change_password' => false])->saveQuietly();

        $request->session()->regenerate();

        return redirect()->route('profile.show')->with('success', 'Password changed successfully.');
    }
}
