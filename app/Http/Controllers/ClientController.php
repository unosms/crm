<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $status = (string) $request->string('status');

        $clients = Client::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('service_plan', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['active', 'suspended', 'cancelled'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->withCount('tickets')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('clients.index', [
            'clients' => $clients,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client): View
    {
        $tickets = $client->tickets()
            ->with(['assignedTo:id,name'])
            ->latest()
            ->paginate(10);

        return view('clients.show', [
            'client' => $client->loadCount('tickets'),
            'tickets' => $tickets,
        ]);
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', ['client' => $client]);
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate($this->rules($client->id));

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        if ($client->tickets()->exists()) {
            return redirect()
                ->route('clients.index')
                ->with('error', 'Cannot delete client with tickets. Close/delete tickets first.');
        }

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?int $clientId = null): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('clients', 'email')->ignore($clientId),
            ],
            'service_plan' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'suspended', 'cancelled'])],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
