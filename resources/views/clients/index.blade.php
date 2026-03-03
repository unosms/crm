<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clients</h2>
            <a href="{{ route('clients.create') }}" class="rounded bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Add Client</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="rounded border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="rounded border border-red-200 bg-red-50 p-4 text-red-700">{{ session('error') }}</div>
            @endif

            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <form method="GET" action="{{ route('clients.index') }}" class="grid gap-3 md:grid-cols-4">
                    <input name="q" value="{{ $search }}" placeholder="Search name / phone / email" class="rounded border-gray-300 md:col-span-2">
                    <select name="status" class="rounded border-gray-300">
                        <option value="">All Status</option>
                        @foreach (['active', 'suspended', 'cancelled'] as $statusOption)
                            <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ ucfirst($statusOption) }}</option>
                        @endforeach
                    </select>
                    <button class="rounded bg-gray-800 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-900">Filter</button>
                </form>
            </div>

            <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Name</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Phone</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Email</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Service Plan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Tickets</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($clients as $client)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $client->full_name }}</td>
                                    <td class="px-4 py-3">{{ $client->phone ?: '-' }}</td>
                                    <td class="px-4 py-3">{{ $client->email ?: '-' }}</td>
                                    <td class="px-4 py-3">{{ $client->service_plan ?: '-' }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statusClass = [
                                                'active' => 'bg-emerald-50 text-emerald-700',
                                                'suspended' => 'bg-amber-50 text-amber-700',
                                                'cancelled' => 'bg-red-50 text-red-700',
                                            ][$client->status] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($client->status) }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ number_format($client->tickets_count) }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('clients.show', $client) }}" class="rounded border border-indigo-200 px-2 py-1 text-indigo-700 hover:bg-indigo-50">View</a>
                                            <a href="{{ route('clients.edit', $client) }}" class="rounded border border-gray-300 px-2 py-1 text-gray-700 hover:bg-gray-50">Edit</a>
                                            <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Delete this client?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded border border-red-200 px-2 py-1 text-red-700 hover:bg-red-50">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">No clients found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-100 p-4">{{ $clients->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
