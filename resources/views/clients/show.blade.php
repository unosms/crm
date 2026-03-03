<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Client Details</h2>
            <div class="flex gap-2">
                <a href="{{ route('tickets.create', ['client_id' => $client->id]) }}" class="rounded bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Create Ticket</a>
                <a href="{{ route('clients.edit', $client) }}" class="rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Edit Client</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="rounded border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <dl class="grid gap-4 md:grid-cols-3">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Full Name</dt>
                        <dd class="mt-1 font-semibold text-gray-900">{{ $client->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Phone</dt>
                        <dd class="mt-1 text-gray-900">{{ $client->phone ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Email</dt>
                        <dd class="mt-1 text-gray-900">{{ $client->email ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Service Plan</dt>
                        <dd class="mt-1 text-gray-900">{{ $client->service_plan ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Status</dt>
                        <dd class="mt-1 text-gray-900">{{ ucfirst($client->status) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Total Tickets</dt>
                        <dd class="mt-1 text-gray-900">{{ number_format($client->tickets_count) }}</dd>
                    </div>
                </dl>

                @if ($client->address)
                    <div class="mt-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Address</p>
                        <p class="mt-1 text-gray-700">{{ $client->address }}</p>
                    </div>
                @endif
                @if ($client->notes)
                    <div class="mt-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Notes</p>
                        <p class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $client->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="border-b border-gray-100 px-4 py-3">
                    <h3 class="font-semibold text-gray-900">Client Tickets</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Ticket</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Subject</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Priority</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Assigned</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Updated</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tickets as $ticket)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3"><a href="{{ route('tickets.show', $ticket) }}" class="text-indigo-600 hover:underline">{{ $ticket->ticket_no }}</a></td>
                                    <td class="px-4 py-3">{{ $ticket->subject }}</td>
                                    <td class="px-4 py-3">{{ str_replace('_', ' ', $ticket->status) }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($ticket->priority) }}</td>
                                    <td class="px-4 py-3">{{ $ticket->assignedTo->name ?? 'Unassigned' }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $ticket->updated_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No tickets for this client.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-100 p-4">{{ $tickets->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
