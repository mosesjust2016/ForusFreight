<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index()
    {
        $staffUsers = User::where('is_admin', true)
            ->orWhereHas('roles')
            ->with('roles')
            ->latest()
            ->get();

        $roles = Role::orderBy('display_name')->get();

        return view('admin.staff.index', compact('staffUsers', 'roles'));
    }

    /**
     * Create a brand new system/staff user (not a promoted client account)
     * with a role assigned at creation. A random password is generated,
     * emailed to them once, and must be changed on first login
     * (must_change_password) — the plaintext value is never stored or
     * shown again after this request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|string|email|max:255|unique:users,email',
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $temporaryPassword = Str::password(14);

        $user = User::create([
            'name'                  => $validated['name'],
            'email'                 => $validated['email'],
            'password'              => $temporaryPassword,
            'is_admin'              => false,
            'is_temporary'          => false,
            'must_change_password'  => true,
            'account_status'        => 'active',
        ]);

        // System users are created and vetted by an admin directly, not
        // through the client OTP-onboarding flow, so they're pre-verified
        // and can log in immediately.
        $user->forceFill([
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ])->save();

        $user->roles()->attach($role->id);

        $emailSent = false;
        try {
            $emailSent = app(BrevoMailService::class)->sendStaffWelcome(
                $user->email,
                $user->name,
                $role->display_name,
                $temporaryPassword
            );
        } catch (\Exception $e) {
            Log::error('Failed to send staff welcome email', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }

        $successMessage = $emailSent
            ? "System user {$user->name} created and emailed their login credentials."
            : "System user {$user->name} created, but the welcome email could not be sent — share the password below with them directly.";

        return back()
            ->with('success', $successMessage)
            ->with('generated_password', $temporaryPassword)
            ->with('generated_password_email', $user->email);
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);

        $user->roles()->syncWithoutDetaching([$request->role_id]);

        return back()->with('success', "Role assigned to {$user->name}.");
    }

    public function removeRole(User $user, Role $role)
    {
        $user->roles()->detach($role->id);

        return back()->with('success', "Role removed from {$user->name}.");
    }
}
