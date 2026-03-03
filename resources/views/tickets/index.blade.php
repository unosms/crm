<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tickets</h2>
            <a href="{{ route('tickets.create') }}" class="rounded bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Create Ticket</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="rounded border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
            @endif

            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <form method="GET" class="grid gap-3 md:grid-cols-5">
                    <input name="q" value="{{ $filters['q'] }}" placeholder="Search ticket/subject/client" class="rounded border-gray-300 md:col-span-2">
                    <select name="status" class="rounded border-gray-300">
                        <option value="">All Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                        @endforeach
                    </select>
                    <select name="priority" class="rounded border-gray-300">
                        <option value="">All Priority</option>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority }}" @selected($filters['priority'] === $priority)>{{ ucfirst($priority) }}</option>
                        @endforeach
                    </select>
                    <select name="assigned_to" class="rounded border-gray-300">
                        <option value="">Any Agent</option>
                        @foreach ($agents as $agent)
                            <option value="{{ $agent->id }}" @selected((string) $agent->id === $filters['assigned_to'])>{{ $agent->name }}</option>
                        @endforeach
                    </select>
                    <button class="rounded bg-gray-800 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-900 md:col-span-5">Filter Tickets</button>
                </form>
            </div>

            <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Ticket</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Client</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Subject</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Priority</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Assigned</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Due</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Updated</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tickets as $ticket)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="font-semibold text-indigo-600 hover:underline">{{ $ticket->ticket_no }}</a>
                                        <p class="text-xs text-gray-500">by {{ $ticket->openedBy->name ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">{{ $ticket->client->full_name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $ticket->subject }}</td>
                                    <td class="px-4 py-3">{{ str_replace('_', ' ', $ticket->status) }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($ticket->priority) }}</td>
                                    <td class="px-4 py-3">{{ $ticket->assignedTo->name ?? 'Unassigned' }}</td>
                                    <td class="px-4 py-3">{{ $ticket->due_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $ticket->updated_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-4 py-6 text-center text-gray-500">No tickets found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-100 p-4">{{ $tickets->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
