@extends('users.layouts.app')

@section('title', 'Packages')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Investment Packages</h1>
                <p class="text-gray-600 mt-1">Choose from our available investment packages</p>
            </div>
        </div>
    </div>

    <!-- Packages Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($packages ?? [] as $package)
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900">{{ $package->name }}</h3>
                <p class="text-3xl font-bold text-blue-600 mt-2">৳{{ number_format($package->amount, 2) }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $package->description ?? 'Investment package' }}</p>
            </div>
            
            <div class="mt-6 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-medium">{{ $package->duration ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Return Rate:</span>
                    <span class="font-medium">{{ $package->return_rate ?? 'N/A' }}%</span>
                </div>
            </div>
            
            <div class="mt-6">
                <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                    Invest Now
                </button>
            </div>
        </div>
        @endforeach
    </div>

    @if(empty($packages))
    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <p class="mt-2 text-sm text-gray-500">No packages available at the moment</p>
    </div>
    @endif
</div>
@endsection
