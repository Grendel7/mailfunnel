<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Http\Request;

class DomainsController extends Controller
{
    public function index(Request $request)
    {
        return view('dashboard.domains.index', [
            'domains' => $request->user()->domains,
        ]);
    }

    public function show(Domain $domain, Request $request)
    {
        $this->authorize('view', $domain);

        return view('dashboard.addresses.index', [
            'addresses' => $domain->addresses()->orderBy('updated_at', 'desc')->paginate(20),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Domain::class);

        return view('dashboard.domains.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Domain::class);

        $request->validate([
            'domain' => [
                'required',
                'unique:domains,domain',
                'regex:/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/',
            ],
        ]);

        $domain = new Domain();
        $domain->domain = $request->get('domain');
        $domain->user_id = $request->user()->id;
        $domain->save();

        return redirect()->route('domains.index')->with('status', 'The domain name has been added!');
    }
}
