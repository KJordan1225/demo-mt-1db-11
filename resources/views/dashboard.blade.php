<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    {{-- Flash messages (success/error) --}}
    @if (session('success') || session('error') || $errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            {{-- Success --}}
            @if (session('success'))
                <div 
                    x-data="{ show: true }" 
                    x-show="show" 
                    x-transition
                    class="mb-3 rounded-md border border-green-200 bg-green-50 text-green-800">
                    <div class="flex items-start justify-between p-3">
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.172 7.707 8.879A1 1 0 006.293 10.293l2 2a1 1 0 001.414 0l4-4Z" clip-rule="evenodd" />
                            </svg>
                            <div class="text-sm font-medium">
                                {{ session('success') }}
                            </div>
                        </div>
                        <button @click="show = false" class="p-1 text-green-700 hover:text-green-900" aria-label="Dismiss">
                            ✕
                        </button>
                    </div>
                </div>
            @endif

            {{-- Error --}}
            @if (session('error'))
                <div 
                    x-data="{ show: true }" 
                    x-show="show" 
                    x-transition
                    class="mb-3 rounded-md border border-red-200 bg-red-50 text-red-800">
                    <div class="flex items-start justify-between p-3">
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10A8 8 0 11.001 9.999 8 8 0 0118 10Zm-9-4a1 1 0 112 0v4a1 1 0 01-2 0V6Zm1 8a1.25 1.25 0 100-2.5A1.25 1.25 0 0010 14z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm font-medium">
                                {{ session('error') }}
                            </div>
                        </div>
                        <button @click="show = false" class="p-1 text-red-700 hover:text-red-900" aria-label="Dismiss">
                            ✕
                        </button>
                    </div>
                </div>
            @endif

            {{-- Validation errors (optional) --}}
            @if ($errors->any())
                <div class="rounded-md border border-yellow-200 bg-yellow-50 text-yellow-900">
                    <div class="p-3">
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.586c.75 1.334-.214 2.99-1.742 2.99H3.48c-1.528 0-2.492-1.656-1.742-2.99L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-2a.75.75 0 01-.75-.75v-3.5a.75.75 0 011.5 0v3.5A.75.75 0 0110 11z" clip-rule="evenodd" />
                            </svg>
                            <div class="text-sm">
                                <span class="font-semibold">Please fix the following:</span>
                                <ul class="mt-2 list-inside list-disc space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
