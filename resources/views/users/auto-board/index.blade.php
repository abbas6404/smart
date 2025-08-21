@extends('users.layouts.app')

@section('title', 'Auto Board')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Auto Board</h1>
        <p class="text-gray-600 mt-2">Manage your auto board participation and earnings</p>
    </div>

    <!-- Auto Board Status -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 {{ $userStatus['is_active'] ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg">
                    <svg class="w-6 h-6 {{ $userStatus['is_active'] ? 'text-green-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Status</p>
                    <p class="text-2xl font-bold {{ $userStatus['is_active'] ? 'text-green-900' : 'text-gray-900' }}">
                        {{ ucfirst($userStatus['status']) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Referrals</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $userStatus['referral_count'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Collection</p>
                    <p class="text-2xl font-bold text-gray-900">৳{{ number_format($userStatus['collection_amount'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto Board Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Auto Board Information</h2>
        
        @if($autoBoard)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Board Details</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Board Name:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $autoBoard->name ?? 'Auto Board' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Required Referrals:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $autoBoard->required_referrals ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Required Investment:</span>
                            <span class="text-sm font-medium text-gray-900">৳{{ number_format($autoBoard->required_investment ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Your Status</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Has Package:</span>
                            <span class="text-sm font-medium {{ $userStatus['has_package'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ $userStatus['has_package'] ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Ready for Distribution:</span>
                            <span class="text-sm font-medium {{ $userStatus['ready_for_distribution'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ $userStatus['ready_for_distribution'] ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Auto Board Available</h3>
                <p class="mt-1 text-sm text-gray-500">Auto board functionality is not currently available.</p>
            </div>
        @endif
    </div>

    <!-- Actions -->
    @if($autoBoard)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if(!$userStatus['is_active'])
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Join Auto Board</h3>
                    <p class="text-sm text-gray-600 mb-4">Join the auto board to start earning automatic income.</p>
                    
                    @if($userStatus['has_package'] && $userStatus['referral_count'] >= ($autoBoard->required_referrals ?? 0))
                        <form action="{{ route('user.auto-board.join') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                Join Auto Board
                            </button>
                        </form>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Requirements Not Met</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            @if(!$userStatus['has_package'])
                                                <li>You need an active investment package</li>
                                            @endif
                                            @if($userStatus['referral_count'] < ($autoBoard->required_referrals ?? 0))
                                                <li>You need at least {{ $autoBoard->required_referrals ?? 0 }} referrals</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Leave Auto Board</h3>
                    <p class="text-sm text-gray-600 mb-4">Leave the auto board if you no longer want to participate.</p>
                    
                    <form action="{{ route('user.auto-board.leave') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            Leave Auto Board
                        </button>
                    </form>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('user.auto-board.earnings') }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">View Earnings</span>
                    </a>
                    <a href="{{ route('user.referrals.index') }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Manage Referrals</span>
                    </a>
                    <a href="{{ route('user.packages.index') }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">View Packages</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
