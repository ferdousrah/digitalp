<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $data = $this->validated($request);

        $user = Auth::user();
        // First address is the default automatically.
        $makeDefault = $data['is_default'] || $user->addresses()->count() === 0;

        $address = $user->addresses()->create($data + ['is_default' => $makeDefault]);

        if ($makeDefault) {
            $this->clearOtherDefaults($address);
        }

        return back()->with('address_success', 'Address saved.');
    }

    public function update(Request $request, Address $address)
    {
        $this->authorizeAddress($address);
        $data = $this->validated($request);

        $address->update($data);

        if ($data['is_default']) {
            $this->clearOtherDefaults($address);
        }

        return back()->with('address_success', 'Address updated.');
    }

    public function destroy(Address $address)
    {
        $this->authorizeAddress($address);
        $wasDefault = $address->is_default;
        $address->delete();

        // Promote the next address to default if we removed the default one.
        if ($wasDefault) {
            $next = Auth::user()->addresses()->first();
            $next?->update(['is_default' => true]);
        }

        return back()->with('address_success', 'Address removed.');
    }

    public function setDefault(Address $address)
    {
        $this->authorizeAddress($address);
        $address->update(['is_default' => true]);
        $this->clearOtherDefaults($address);

        return back()->with('address_success', 'Default address updated.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'label'      => ['nullable', 'string', 'max:20'],
            'name'       => ['required', 'string', 'max:120'],
            'phone'      => ['required', 'string', 'max:20'],
            'district'   => ['required', 'string', 'max:60'],
            'thana'      => ['required', 'string', 'max:60'],
            'address'    => ['required', 'string', 'max:500'],
            'is_default' => ['nullable', 'boolean'],
        ]) + ['is_default' => $request->boolean('is_default')];
    }

    protected function authorizeAddress(Address $address): void
    {
        abort_unless($address->user_id === Auth::id(), 403);
    }

    protected function clearOtherDefaults(Address $address): void
    {
        Auth::user()->addresses()
            ->whereKeyNot($address->getKey())
            ->update(['is_default' => false]);
    }
}
