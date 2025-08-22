@extends('users.layouts.app')

@section('title', 'Switch Account')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Switch Account</h1>
            <p class="text-gray-600">Choose which sub-account you want to work with</p>
        </div>

        <!-- Current Account Info -->
        @if($currentAccount)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-blue-900 mb-2">Currently Active</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-sm font-medium text-blue-700">Account Name</span>
                            <p class="text-lg font-semibold text-blue-900">{{ $currentAccount->name }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-blue-700">Account Number</span>
                            <p class="text-lg font-mono text-blue-900">{{ $currentAccount->account_number }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-blue-700">Balance</span>
                            <p class="text-lg font-semibold text-green-600">৳{{ number_format($currentAccount->total_balance, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Active
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- Available Accounts -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Available Accounts</h3>
                <p class="text-sm text-gray-600 mt-1">Click on an account to switch to it</p>
            </div>
            
            <div class="p-6">
                @if($allAccounts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($allAccounts as $account)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow duration-200 
                                    {{ $account->id === ($currentAccount->id ?? null) ? 'border-blue-300 bg-blue-50' : 'border-gray-200' }}">
                            
                            <!-- Account Header -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-blue-600">{{ substr($account->name, 0, 2) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $account->name }}</h4>
                                        @if($account->is_primary)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Primary
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($account->id === ($currentAccount->id ?? null))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Current
                                    </span>
                                @endif
                            </div>

                            <!-- Account Details -->
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Account Number:</span>
                                    <span class="font-mono text-gray-900">{{ $account->account_number }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Referral Code:</span>
                                    <span class="font-mono text-gray-900">{{ $account->referral_code }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Balance:</span>
                                    <span class="font-semibold text-gray-900">৳{{ number_format($account->total_balance, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Status:</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                        {{ $account->status === 'active' ? 'bg-green-100 text-green-800' : 
                                           ($account->status === 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($account->status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                @if($account->id !== ($currentAccount->id ?? null))
                                    <form action="{{ route('user.accounts.switch-account') }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="sub_account_id" value="{{ $account->id }}">
                                        <button type="submit" 
                                                class="w-full inline-flex justify-center items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                            </svg>
                                            Switch to This Account
                                        </button>
                                    </form>
                                @else
                                    <button disabled 
                                            class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Currently Active
                                    </button>
                                @endif
                                
                                <a href="{{ route('user.accounts.show', $account) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No accounts found</h3>
                        <p class="mt-1 text-sm text-gray-500">You don't have any sub-accounts yet.</p>
                        <div class="mt-6">
                            <a href="{{ route('user.accounts.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create Your First Account
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 flex justify-center space-x-4">
            <a href="{{ route('user.accounts.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Accounts
            </a>
            
            <a href="{{ route('user.accounts.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create New Account
            </a>
        </div>

        <!-- Information Box -->
        <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-800">About Account Switching</h3>
                    <div class="mt-2 text-sm text-gray-700 space-y-1">
                        <p>• You can switch between your sub-accounts at any time</p>
                        <p>• Each account maintains its own balance, packages, and referral network</p>
                        <p>• Your current active account will be highlighted in blue</p>
                        <p>• Switching accounts will update your dashboard and all related features</p>
                        <p>• You can always return to your primary account from the accounts page</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
