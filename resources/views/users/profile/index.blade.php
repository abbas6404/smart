@extends('users.layouts.app')

@section('title', 'Profile')

@section('content')
<div class="space-y-6">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center space-x-6">
            <div class="flex-shrink-0">
                <div class="w-24 h-24 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center">
                                    @if($user->subAccount?->profile_picture)
                    <img src="{{ asset('storage/' . $user->subAccount->profile_picture) }}" alt="Profile" class="w-24 h-24 rounded-full object-cover">
                @else
                    <span class="text-2xl font-bold text-white">{{ strtoupper(substr($user->subAccount?->name ?? $user->name, 0, 2)) }}</span>
                @endif
                </div>
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->subAccount?->name ?? $user->name }}</h1>
                <p class="text-gray-600">Account #{{ $user->subAccount?->account_number ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">Member since {{ $user->subAccount?->created_at?->format('F Y') ?? $user->created_at->format('F Y') }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 text-sm font-medium rounded-full 
                    {{ ($user->subAccount?->status ?? 'inactive') === 'active' ? 'bg-green-100 text-green-800' : 
                       (($user->subAccount?->status ?? 'inactive') === 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                    {{ ucfirst($user->subAccount?->status ?? 'inactive') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Account Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Full Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Referral Code</label>
                    <div class="mt-1 flex items-center space-x-2">
                        <code class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm">{{ $user->subAccount?->referral_code ?? 'N/A' }}</code>
                        <button onclick="copyToClipboard('{{ $user->subAccount?->referral_code ?? '' }}')" class="text-blue-600 hover:text-blue-800 text-sm">
                            Copy
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Account Number</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->subAccount?->account_number ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- MLM Information -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">MLM Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sponsor</label>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($user->sponsor)
                            {{ $user->sponsor->name }} ({{ $user->sponsor->subAccount?->account_number ?? 'N/A' }})
                        @else
                            <span class="text-gray-500">No sponsor</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Direct Referrals</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->subAccount?->direct_referral_count ?? 0 }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Generation Count</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $user->subAccount?->generation_count ?? 0 }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Active Package</label>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($user->activePackage)
                            {{ $user->activePackage->name }} - ৳{{ number_format($user->activePackage->amount, 2) }}
                        @else
                            <span class="text-gray-500">No active package</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Financial Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Total Balance</p>
                <p class="text-2xl font-bold text-gray-900">৳{{ number_format($user->subAccount?->total_balance ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Total Deposits</p>
                <p class="text-2xl font-bold text-gray-900">৳{{ number_format($user->subAccount?->total_deposit ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Total Withdrawals</p>
                <p class="text-2xl font-bold text-gray-900">৳{{ number_format($user->subAccount?->total_withdrawal ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Withdrawal Limit</p>
                <p class="text-2xl font-bold text-gray-900">৳{{ number_format($user->subAccount?->withdrawal_limit ?? 0, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Commission Breakdown -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Commission Breakdown</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-600">Sponsor Commission</p>
                <p class="text-2xl font-bold text-blue-900">৳{{ number_format($user->subAccount?->total_sponsor_commission ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-sm text-purple-600">Generation Commission</p>
                <p class="text-2xl font-bold text-purple-900">৳{{ number_format($user->subAccount?->total_generation_commission ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-4 bg-orange-50 rounded-lg">
                <p class="text-sm text-orange-600">Auto Income</p>
                <p class="text-2xl font-bold text-orange-900">৳{{ number_format($user->subAccount?->total_auto_income ?? 0, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
            <a href="{{ route('user.transactions.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
        </div>
        
        @if(isset($recentTransactions) && $recentTransactions->count() > 0)
            <div class="space-y-3">
                @foreach($recentTransactions->take(10) as $transaction)
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
                            <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium 
                            {{ $transaction->type === 'deposit' ? 'text-green-600' : 
                               ($transaction->type === 'withdrawal' ? 'text-red-600' : 'text-blue-600') }}">
                            {{ $transaction->type === 'withdrawal' ? '-' : '+' }}৳{{ number_format($transaction->amount, 2) }}
                        </p>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $transaction->type === 'approved' ? 'bg-green-100 text-green-800' : 
                               ($transaction->type === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
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
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // You can add a toast notification here
        alert('Referral code copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endsection
