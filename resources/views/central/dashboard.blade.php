<x-central-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Central Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Stats Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Total Tenants</p>
                    <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $stats['total_tenants'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Active Tenants</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">{{ $stats['active_tenants'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Logged in as</p>
                    <p class="mt-2 text-base font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full">
                        Central Admin
                    </span>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('central.profile.edit') }}"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300
                                  rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest
                                  hover:bg-gray-50 transition">
                            {{ __('My Profile') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-central-app-layout>
