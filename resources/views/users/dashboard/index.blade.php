@extends('users.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="text-gray-600 mt-1">Here's what's happening with your MLM business today.</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Today's Date</p>
                <p class="text-lg font-semibold text-gray-900">{{ now()->format('F j, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Balance -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Balance</p>
                    <p class="text-2xl font-bold text-gray-900">৳{{ number_format($user->subAccount?->total_balance ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Direct Referrals -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Direct Referrals</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $user->subAccount?->direct_referral_count ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Total Commissions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Commissions</p>
                    <p class="text-2xl font-bold text-gray-900">৳{{ number_format(($user->subAccount?->total_sponsor_commission ?? 0) + ($user->subAccount?->total_generation_commission ?? 0), 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Auto Income -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Auto Income</p>
                    <p class="text-2xl font-bold text-gray-900">৳{{ number_format($user->subAccount?->total_auto_income ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto Board Status -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Today's Auto Board Status</h2>
            <span class="px-3 py-1 text-sm font-medium rounded-full 
                {{ $autoBoardStats['status'] === 'collection' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                {{ ucfirst($autoBoardStats['status'] ?? 'collection') }}
            </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-sm text-gray-500">Collection Amount</p>
                <p class="text-xl font-bold text-gray-900">৳{{ number_format($autoBoardStats['collection_amount'] ?? 0, 2) }}</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500">Contributors</p>
                <p class="text-xl font-bold text-gray-900">{{ $autoBoardStats['contributors'] ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500">Eligible Accounts</p>
                <p class="text-xl font-bold text-gray-900">{{ $autoBoardStats['eligible_accounts'] ?? 0 }}</p>
            </div>
        </div>
        
        @if($autoBoardStats['ready_for_distribution'] ?? false)
            <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800">
                    <strong>Ready for Distribution!</strong> Today's collection will be distributed tomorrow at midnight.
                </p>
            </div>
        @endif
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
        
        @if(isset($recentTransactions) && $recentTransactions->count() > 0)
            <div class="space-y-3">
                @foreach($recentTransactions->take(5) as $transaction)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                            {{ $transaction->type === 'deposit' ? 'bg-green-100' : 
                               ($transaction->type === 'withdrawal' ? 'bg-red-100' : 'bg-blue-100') }}">
                            <svg class="w-4 h-4 
                                {{ $transaction->type === 'deposit' ? 'text-green-600' : 
                                   ($transaction->type === 'withdrawal' ? 'text-red-600' : 'text-blue-600') }}" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="{{ $transaction->type === 'deposit' ? 'M7 11l5-5m0 0l5 5m-5-5v12' : 
                                        ($transaction->type === 'withdrawal' ? 'M7 13l5 5m0 0l5-5m-5 5V6' : 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1') }}">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">
                                {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $transaction->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium 
                            {{ $transaction->type === 'deposit' ? 'text-green-600' : 
                               ($transaction->type === 'withdrawal' ? 'text-red-600' : 'text-blue-600') }}">
                            {{ $transaction->type === 'withdrawal' ? '-' : '+' }}৳{{ number_format($transaction->amount, 2) }}
                        </p>
                        <p class="text-xs text-gray-500">{{ ucfirst($transaction->status) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 00-2 2v2h2V7z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500">No recent transactions</p>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('user.packages.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Buy Package</p>
                    <p class="text-sm text-gray-500">Invest in new packages</p>
                </div>
            </a>

            <a href="{{ route('user.referrals.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Invite Friends</p>
                    <p class="text-sm text-gray-500">Share your referral link</p>
                </div>
            </a>

            <a href="{{ route('user.transactions.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 00-2 2v2h2V7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">View History</p>
                    <p class="text-sm text-gray-500">Check all transactions</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
