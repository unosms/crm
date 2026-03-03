<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ticket {{ $ticket->ticket_no }}</h2>
                <p class="text-sm text-gray-500">{{ $ticket->subject }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tickets.edit', $ticket) }}" class="rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Edit</a>
                <a href="{{ route('tickets.index') }}" class="rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Back</a>
                @if (auth()->user()->isAdmin() || $ticket->opened_by === auth()->id())
                    <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Delete this ticket?')">
                        @csrf
                        @method('DELETE')
                        <button class="rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Delete</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="rounded border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
            @endif

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900">Description</h3>
                    <p class="mt-3 whitespace-pre-wrap text-gray-700">{{ $ticket->description }}</p>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    @php
                        $statusClass = [
                            'open' => 'bg-blue-50 text-blue-700',
                            'in_progress' => 'bg-amber-50 text-amber-700',
                            'pending' => 'bg-orange-50 text-orange-700',
                            'resolved' => 'bg-emerald-50 text-emerald-700',
                            'closed' => 'bg-slate-100 text-slate-700',
                        ][$ticket->status] ?? 'bg-gray-100 text-gray-700';

                        $priorityClass = [
                            'low' => 'bg-slate-100 text-slate-700',
                            'medium' => 'bg-blue-50 text-blue-700',
                            'high' => 'bg-orange-50 text-orange-700',
                            'critical' => 'bg-red-50 text-red-700',
                        ][$ticket->priority] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <h3 class="text-lg font-semibold text-gray-900">Details</h3>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-gray-500">Status</dt>
                            <dd><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $statusClass }}">{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</span></dd>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-gray-500">Priority</dt>
                            <dd><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $priorityClass }}">{{ ucfirst($ticket->priority) }}</span></dd>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-gray-500">Client</dt>
                            <dd class="font-medium text-gray-900">{{ $ticket->client->full_name ?? '-' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-gray-500">Source</dt>
                            <dd class="font-medium text-gray-900">{{ ucfirst($ticket->source) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-gray-500">Opened By</dt>
                            <dd class="font-medium text-gray-900">{{ $ticket->openedBy->name ?? '-' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-gray-500">Assigned To</dt>
                            <dd class="font-medium text-gray-900">{{ $ticket->assignedTo->name ?? 'Unassigned' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-gray-500">Due</dt>
                            <dd class="font-medium text-gray-900">{{ $ticket->due_at?->format('Y-m-d H:i') ?? '-' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-gray-500">Resolved</dt>
                            <dd class="font-medium text-gray-900">{{ $ticket->resolved_at?->format('Y-m-d H:i') ?? '-' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <dt class="text-gray-500">Created</dt>
                            <dd class="font-medium text-gray-900">{{ $ticket->created_at->format('Y-m-d H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900">Comments</h3>

                    <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" class="mt-4 space-y-3">
                        @csrf
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Add Comment</label>
                            <textarea name="comment" rows="4" required class="w-full rounded border-gray-300">{{ old('comment') }}</textarea>
                            @error('comment') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300">
                            Internal note (not for client communication)
                        </label>
                        <div>
                            <button class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Post Comment</button>
                        </div>
                    </form>

                    <div class="mt-6 space-y-4">
                        @forelse ($ticket->comments as $comment)
                            <div class="rounded border border-gray-200 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="font-semibold text-gray-900">{{ $comment->user->name ?? '-' }}</p>
                                    <div class="flex items-center gap-2">
                                        @if ($comment->is_internal)
                                            <span class="rounded-full bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700">Internal</span>
                                        @endif
                                        <span class="text-xs text-gray-500">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                </div>
                                <p class="mt-2 whitespace-pre-wrap text-sm text-gray-700">{{ $comment->comment }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No comments yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    <div class="mt-4 space-y-2 text-sm">
                        <a href="{{ route('clients.show', $ticket->client_id) }}" class="block rounded border border-gray-300 px-3 py-2 font-semibold text-gray-700 hover:bg-gray-50">Open Client Profile</a>
                        <a href="{{ route('tickets.edit', $ticket) }}" class="block rounded border border-gray-300 px-3 py-2 font-semibold text-gray-700 hover:bg-gray-50">Update Ticket</a>
                        <a href="{{ route('tickets.create', ['client_id' => $ticket->client_id]) }}" class="block rounded border border-gray-300 px-3 py-2 font-semibold text-gray-700 hover:bg-gray-50">Create New Ticket for Client</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
