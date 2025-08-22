@extends('users.layouts.app')

@section('title', 'Account Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $subAccount->name }}</h1>
                    <p class="text-gray-600">Account Details and Information</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('user.accounts.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Accounts
                    </a>
                    
                    @if($subAccount->id !== ($currentAccount->id ?? null))
                        <form action="{{ route('user.accounts.switch-account') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="sub_account_id" value="{{ $subAccount->id }}">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Switch to This Account
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Account Status Banner -->
        @if($subAccount->id === ($currentAccount->id ?? null))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Currently Active Account</h3>
                    <p class="mt-1 text-sm text-blue-700">This is your currently active account. All actions will be performed on this account.</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Account Information Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Account Name:</span>
                        <span class="text-sm text-gray-900">{{ $subAccount->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Account Number:</span>
                        <span class="text-sm font-mono text-gray-900">{{ $subAccount->account_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Referral Code:</span>
                        <span class="text-sm font-mono text-gray-900">{{ $subAccount->referral_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $subAccount->status === 'active' ? 'bg-green-100 text-green-800' : 
                               ($subAccount->status === 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($subAccount->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Account Type:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $subAccount->is_primary ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $subAccount->is_primary ? 'Primary' : 'Secondary' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Created:</span>
                        <span class="text-sm text-gray-900">{{ $subAccount->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Financial Information -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Financial Summary</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Total Balance:</span>
                        <span class="text-lg font-semibold text-green-600">৳{{ number_format($subAccount->total_balance, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Withdrawal Limit:</span>
                        <span class="text-sm text-gray-900">৳{{ number_format($subAccount->withdrawal_limit, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Total Withdrawal:</span>
                        <span class="text-sm text-gray-900">৳{{ number_format($subAccount->total_withdrawal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Package Purchases:</span>
                        <span class="text-sm text-gray-900">৳{{ number_format($subAccount->total_package_purchase, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Total Deposits:</span>
                        <span class="text-sm text-gray-900">৳{{ number_format($subAccount->total_deposit, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Information -->
        <div class="bg-white rounded-lg shadow-md mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Commission & Referral Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $subAccount->direct_referral_count }}</div>
                        <div class="text-sm text-gray-500">Direct Referrals</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $subAccount->generation_count }}</div>
                        <div class="text-sm text-gray-500">Generation Count</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">৳{{ number_format($subAccount->total_sponsor_commission + $subAccount->total_generation_commission, 2) }}</div>
                        <div class="text-sm text-gray-500">Total Commissions</div>
                    </div>
                </div>
                
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Sponsor Commission:</span>
                        <span class="text-sm text-gray-900">৳{{ number_format($subAccount->total_sponsor_commission, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Generation Commission:</span>
                        <span class="text-sm text-gray-900">৳{{ number_format($subAccount->total_generation_commission, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Auto Income:</span>
                        <span class="text-sm text-gray-900">৳{{ number_format($subAccount->total_auto_income, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Actions -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Account Actions</h3>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap gap-4">
                    @if(!$subAccount->is_primary)
                        <form action="{{ route('user.accounts.set-primary', $subAccount) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Set as Primary Account
                            </button>
                        </form>
                    @endif
                    
                    @if(!$subAccount->is_primary && $subAccount->total_balance == 0 && !$subAccount->active_package_id)
                        <form action="{{ route('user.accounts.destroy', $subAccount) }}" method="POST" class="inline" 
                              onsubmit="return confirm('Are you sure you want to delete this account? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Account
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('user.dashboard.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Go to Dashboard
                    </a>
                </div>
                
                @if($subAccount->is_primary)
                    <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-800">This is your primary account. It cannot be deleted and serves as your main MLM account.</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if($subAccount->active_package_id)
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800">This account has an active package and cannot be deleted until the package expires.</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if($subAccount->total_balance > 0)
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-800">This account has a balance of ৳{{ number_format($subAccount->total_balance, 2) }}. Please withdraw funds before deleting.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
