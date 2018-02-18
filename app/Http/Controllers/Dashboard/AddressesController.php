<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressesController extends Controller
{

    public function index(Request $request)
    {
        $addresses = Address::whereHas('domain', function ($query) use ($request) {
            return $query->where('user_id', $request->user()->id);
        })->orderBy('updated_at', 'desc')->paginate(20);

        return view('dashboard.addresses.index', [
            'addresses' => $addresses,
        ]);
    }

    public function show(Address $address)
    {
        $this->authorize('view', $address);

        return view('dashboard.messages.index', [
            'messages' => $address->messages()->paginate(20),
        ]);
    }

    public function edit(Address $address)
    {
        $this->authorize('update', $address);

        return view('dashboard.addresses.edit', [
            'address' => $address,
        ]);
    }

    public function update(Address $address, Request $request)
    {
        $this->authorize('update', $address);

        $request->validate([
            'is_blocked' => 'required|boolean',
        ]);

        $address->is_blocked = $request->get('is_blocked');
        $address->save();

        return redirect()->back()->with('status', 'The address has been updated!');
    }
}
