<?php

namespace App\Http\Controllers;

use App\Models\PhoneCountry;
use Illuminate\Http\Request;

class PhoneCountryController extends Controller
{
    public function index()
    {
        $countries = PhoneCountry::orderBy('name')->get();
        return view('admin.settings.phone-countries', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'dial_code' => ['required', 'string', 'max:10', 'regex:/^\d+$/', 'unique:phone_countries,dial_code'],
            'flag'      => ['nullable', 'string', 'max:10'],
        ], [
            'dial_code.unique' => 'That dial code is already registered.',
            'dial_code.regex'  => 'Dial code must be digits only (no + sign).',
        ]);

        PhoneCountry::create($validated + ['is_active' => true]);

        return back()->with('success', "Country \"{$validated['name']}\" added.");
    }

    public function toggle(PhoneCountry $phoneCountry)
    {
        $phoneCountry->update(['is_active' => ! $phoneCountry->is_active]);

        $status = $phoneCountry->is_active ? 'enabled' : 'disabled';
        return back()->with('success', "{$phoneCountry->name} {$status}.");
    }

    public function destroy(PhoneCountry $phoneCountry)
    {
        $name = $phoneCountry->name;
        $phoneCountry->delete();

        return back()->with('success', "\"{$name}\" removed.");
    }
}
