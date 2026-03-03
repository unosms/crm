<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $status = (string) $request->string('status');
        $priority = (string) $request->string('priority');
        $assignedTo = (string) $request->string('assigned_to');

        $tickets = Ticket::query()
            ->with(['client:id,full_name', 'assignedTo:id,name', 'openedBy:id,name'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ticket_no', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($clientQuery) use ($search) {
                            $clientQuery->where('full_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when(in_array($status, $this->statuses(), true), fn ($query) => $query->where('status', $status))
            ->when(in_array($priority, $this->priorities(), true), fn ($query) => $query->where('priority', $priority))
            ->when(is_numeric($assignedTo), fn ($query) => $query->where('assigned_to', (int) $assignedTo))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('tickets.index', [
            'tickets' => $tickets,
            'agents' => User::query()->orderBy('name')->get(['id', 'name']),
            'filters' => [
                'q' => $search,
                'status' => $status,
                'priority' => $priority,
                'assigned_to' => $assignedTo,
            ],
            'statuses' => $this->statuses(),
            'priorities' => $this->priorities(),
        ]);
    }

    public function create(): View
    {
        return view('tickets.create', [
            'clients' => Client::query()->orderBy('full_name')->get(['id', 'full_name']),
            'agents' => User::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => $this->statuses(),
            'priorities' => $this->priorities(),
            'sources' => $this->sources(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $validated['ticket_no'] = $this->nextTicketNo();
        $validated['opened_by'] = Auth::id();

        if (in_array($validated['status'], ['resolved', 'closed'], true)) {
            $validated['resolved_at'] = now();
        }

        $ticket = Ticket::create($validated);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load([
            'client',
            'openedBy:id,name',
            'assignedTo:id,name',
            'comments' => fn ($query) => $query->latest(),
            'comments.user:id,name',
        ]);

        return view('tickets.show', [
            'ticket' => $ticket,
            'statuses' => $this->statuses(),
            'priorities' => $this->priorities(),
            'sources' => $this->sources(),
            'agents' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function edit(Ticket $ticket): View
    {
        return view('tickets.edit', [
            'ticket' => $ticket,
            'clients' => Client::query()->orderBy('full_name')->get(['id', 'full_name']),
            'agents' => User::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => $this->statuses(),
            'priorities' => $this->priorities(),
            'sources' => $this->sources(),
        ]);
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $isResolved = in_array($validated['status'], ['resolved', 'closed'], true);
        $wasResolved = in_array($ticket->status, ['resolved', 'closed'], true);

        if ($isResolved && ! $wasResolved) {
            $validated['resolved_at'] = now();
        } elseif (! $isResolved && $wasResolved) {
            $validated['resolved_at'] = null;
        }

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isAdmin() && $ticket->opened_by !== $user->id) {
            abort(403, 'Only admins or ticket creator can delete this ticket.');
        }

        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', Rule::in($this->priorities())],
            'status' => ['required', Rule::in($this->statuses())],
            'source' => ['required', Rule::in($this->sources())],
            'due_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return list<string>
     */
    private function statuses(): array
    {
        return ['open', 'in_progress', 'pending', 'resolved', 'closed'];
    }

    /**
     * @return list<string>
     */
    private function priorities(): array
    {
        return ['low', 'medium', 'high', 'critical'];
    }

    /**
     * @return list<string>
     */
    private function sources(): array
    {
        return ['phone', 'whatsapp', 'email', 'visit', 'other'];
    }

    private function nextTicketNo(): string
    {
        $prefix = 'TKT-' . now()->format('Ymd');

        $lastToday = Ticket::query()
            ->whereDate('created_at', now()->toDateString())
            ->latest('id')
            ->value('ticket_no');

        $next = 1;
        if (is_string($lastToday) && str_contains($lastToday, '-')) {
            $parts = explode('-', $lastToday);
            $lastPart = end($parts);
            if (is_string($lastPart) && ctype_digit($lastPart)) {
                $next = (int) $lastPart + 1;
            }
        }

        return sprintf('%s-%04d', $prefix, $next);
    }
}
