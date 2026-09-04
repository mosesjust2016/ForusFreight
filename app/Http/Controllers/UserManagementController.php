<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $staffUsers = User::where('is_admin', true)
            ->orWhereHas('roles')
            ->with('roles')
            ->latest()
            ->get();

        $allUsers = User::where('is_admin', false)
            ->whereDoesntHave('roles')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $roles = Role::orderBy('display_name')->get();

        return view('admin.staff.index', compact('staffUsers', 'allUsers', 'roles'));
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
