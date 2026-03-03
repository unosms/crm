<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Ticket</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Client</label>
                            <select name="client_id" required class="w-full rounded border-gray-300">
                                <option value="">Select Client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" @selected((string) old('client_id', $ticket->client_id) === (string) $client->id)>{{ $client->full_name }}</option>
                                @endforeach
                            </select>
                            @error('client_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Assign To</label>
                            <select name="assigned_to" class="w-full rounded border-gray-300">
                                <option value="">Unassigned</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}" @selected((string) old('assigned_to', $ticket->assigned_to) === (string) $agent->id)>{{ $agent->name }}</option>
                                @endforeach
                            </select>
                            @error('assigned_to') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Priority</label>
                            <select name="priority" class="w-full rounded border-gray-300">
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority }}" @selected(old('priority', $ticket->priority) === $priority)>{{ ucfirst($priority) }}</option>
                                @endforeach
                            </select>
                            @error('priority') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" class="w-full rounded border-gray-300">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected(old('status', $ticket->status) === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                                @endforeach
                            </select>
                            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Source</label>
                            <select name="source" class="w-full rounded border-gray-300">
                                @foreach ($sources as $source)
                                    <option value="{{ $source }}" @selected(old('source', $ticket->source) === $source)>{{ ucfirst($source) }}</option>
                                @endforeach
                            </select>
                            @error('source') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Due At</label>
                            <input
                                type="datetime-local"
                                name="due_at"
                                value="{{ old('due_at', $ticket->due_at?->format('Y-m-d\TH:i')) }}"
                                class="w-full rounded border-gray-300"
                            >
                            @error('due_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Subject</label>
                        <input name="subject" value="{{ old('subject', $ticket->subject) }}" required class="w-full rounded border-gray-300">
                        @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="6" required class="w-full rounded border-gray-300">{{ old('description', $ticket->description) }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-3">
                        <button class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Update Ticket</button>
                        <a href="{{ route('tickets.show', $ticket) }}" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
