<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\SchoolEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Browse campus user accounts.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $role = $request->query('role', 'all');

        $query = User::latest();

        if ($search !== '') {
            $pattern = '%' . $search . '%';
            $query->where(function ($q) use ($pattern) {
                $q->where('name', 'ILIKE', $pattern)
                    ->orWhere('email', 'ILIKE', $pattern)
                    ->orWhere('school_id', 'ILIKE', $pattern);
            });
        }

        if (in_array($role, ['user', 'admin'])) {
            $query->where('role', $role);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    /**
     * Create a new campus user account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', new SchoolEmail()],
            'role' => ['required', Rule::enum(UserRole::class)],
            'department' => ['nullable', 'string', 'max:100'],
        ]);

        $temporaryPassword = Str::random(10) . 'A1!';

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($temporaryPassword);
        $user->role = $validated['role'];
        $user->department = $validated['department'] ?? null;
        $user->is_active = true;
        $user->must_change_password = true;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "Account created for {$user->name}. Temporary Password: {$temporaryPassword}");
    }

    /**
     * Toggle active/deactivated state of an account.
     */
    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot deactivate your own administrative account.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $action = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Account for {$user->name} has been {$action}.");
    }

    /**
     * Toggle user/admin role.
     */
    public function toggleRole(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot alter your own administrative role.');
        }

        $user->role = ($user->role === UserRole::Admin) ? UserRole::User : UserRole::Admin;
        $user->save();

        return back()->with('success', "Role for {$user->name} updated to {$user->role->label()}.");
    }

    /**
     * Reset a user's password with a new temporary password.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $tempPassword = Str::random(10) . 'A1!';

        $user->password = Hash::make($tempPassword);
        $user->must_change_password = true;
        $user->save();

        return back()->with('success', "Temporary password generated for {$user->name}: {$tempPassword}");
    }
}
