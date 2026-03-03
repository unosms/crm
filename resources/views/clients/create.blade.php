<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Client</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <form method="POST" action="{{ route('clients.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Full Name</label>
                            <input name="full_name" value="{{ old('full_name') }}" required class="w-full rounded border-gray-300">
                            @error('full_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Phone</label>
                            <input name="phone" value="{{ old('phone') }}" class="w-full rounded border-gray-300">
                            @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded border-gray-300">
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Service Plan</label>
                            <input name="service_plan" value="{{ old('service_plan') }}" placeholder="e.g. Fiber 100 Mbps" class="w-full rounded border-gray-300">
                            @error('service_plan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" class="w-full rounded border-gray-300">
                                @foreach (['active', 'suspended', 'cancelled'] as $statusOption)
                                    <option value="{{ $statusOption }}" @selected(old('status', 'active') === $statusOption)>{{ ucfirst($statusOption) }}</option>
                                @endforeach
                            </select>
                            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" rows="2" class="w-full rounded border-gray-300">{{ old('address') }}</textarea>
                        @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" rows="4" class="w-full rounded border-gray-300">{{ old('notes') }}</textarea>
                        @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-3">
                        <button class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save Client</button>
                        <a href="{{ route('clients.index') }}" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
