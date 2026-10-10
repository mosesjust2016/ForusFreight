<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Invoice;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Staff keep the old profile page; customers get the single Account
        // page that folds in password, invoices and help (Phase 3).
        if ($user->isStaff()) {
            return view('client.profile.index', [
                'user' => $user,
            ]);
        }

        $ownInvoices = fn () => Invoice::whereHas('shipment', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });
        $pending = $ownInvoices()->where('status', 'Pending');

        return view('client.account', [
            'user' => $user,
            'invoices' => $ownInvoices()->latest()->paginate(10)->withQueryString(),
            'outstanding' => (clone $pending)->sum('amount'),
            'outstandingCurrency' => (clone $pending)->value('currency'),
        ]);
    }

    public function security()
    {
        // The old standalone page is now a section of the Account page.
        if (! Auth::user()->isStaff()) {
            return redirect()->to(route('client.profile') . '#password');
        }

        return view('client.profile.security');
    }

    public function settings()
    {
        // The settings page was a stub — its toggles and selects had no
        // name attributes and no POST route, so nothing ever persisted.
        // Both URLs (customer and staff) now land on the real account page.
        return redirect()->route(Auth::user()->isStaff() ? 'admin.profile' : 'client.profile');
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
        // current_password validates against the DEFAULT guard ('web')
        // unless told otherwise — a staff member has no 'web' session at
        // all, so without this the check would always fail for them.
        $guard = Auth::user()->isStaff() ? 'admin' : 'web';

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', "current_password:{$guard}"],
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
        // Help is a section of the Account page now (staff keep the old one).
        if (! Auth::user()->isStaff()) {
            return redirect()->to(route('client.profile') . '#help');
        }

        return view('client.help');
    }

    public function gettingStarted()
    {
        return view('client.getting-started');
    }

    /* ──────────────────────────────────────────────────────────
       Forced password change (new system users)
       ────────────────────────────────────────────────────────── */

    public function forcePasswordChange()
    {
        return view('admin.force-password-change');
    }

    public function updateForcedPassword(Request $request)
    {
        $validated = $request->validateWithBag('updateForcedPassword', [
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => $validated['password'],
            'must_change_password' => false,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Password changed successfully. Welcome to Forus Freight.');
    }
}
