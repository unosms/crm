<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ISP CRM Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
            @endif

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Clients</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($dashboardStats['clients_total']) }}</p>
                    <p class="mt-1 text-sm text-gray-500">Active: {{ number_format($dashboardStats['clients_active']) }}</p>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Tickets</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($dashboardStats['tickets_total']) }}</p>
                    <p class="mt-1 text-sm text-gray-500">Open + Pending: {{ number_format($dashboardStats['tickets_open']) }}</p>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Resolved Today</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($dashboardStats['tickets_resolved_today']) }}</p>
                    <p class="mt-1 text-sm text-gray-500">Unassigned open: {{ number_format($dashboardStats['tickets_unassigned']) }}</p>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">My Assigned Open</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($myAssignedOpen) }}</p>
                    <p class="mt-1 text-sm text-gray-500">Work queue</p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 lg:grid-cols-3">
                @php
                    $statusColors = [
                        'open' => 'bg-blue-50 text-blue-700',
                        'in_progress' => 'bg-amber-50 text-amber-700',
                        'pending' => 'bg-orange-50 text-orange-700',
                        'resolved' => 'bg-emerald-50 text-emerald-700',
                        'closed' => 'bg-slate-100 text-slate-700',
                    ];
                @endphp
                @foreach (['open', 'in_progress', 'pending', 'resolved', 'closed'] as $status)
                    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ str_replace('_', ' ', $status) }}</p>
                        <div class="mt-3 inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $statusColors[$status] }}">
                            {{ number_format((int) ($ticketStatusTotals[$status] ?? 0)) }} tickets
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Tickets</h3>
                    <a href="{{ route('tickets.create') }}" class="rounded bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">New Ticket</a>
                </div>
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
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Updated</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recentTickets as $ticket)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3"><a class="text-indigo-600 hover:underline" href="{{ route('tickets.show', $ticket) }}">{{ $ticket->ticket_no }}</a></td>
                                    <td class="px-4 py-3">{{ $ticket->client->full_name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $ticket->subject }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ str_replace('_', ' ', $ticket->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ ucfirst($ticket->priority) }}</td>
                                    <td class="px-4 py-3">{{ $ticket->assignedTo->name ?? 'Unassigned' }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $ticket->updated_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">No tickets yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
