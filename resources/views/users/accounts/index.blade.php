@extends('users.layouts.app')

@section('title', 'My Accounts')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Accounts</h1>
            <p class="text-gray-600">Manage your MLM sub-accounts and switch between them</p>
        </div>

        <!-- Current Account Info -->
        @if($currentAccount)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Current Active Account</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Account Name</span>
                            <p class="text-lg font-semibold text-gray-900">{{ $currentAccount->name }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Account Number</span>
                            <p class="text-lg font-mono text-gray-900">{{ $currentAccount->account_number }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Balance</span>
                            <p class="text-lg font-semibold text-green-600">৳{{ number_format($currentAccount->total_balance, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('user.accounts.switch') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        Switch Account
                    </a>
                    <a href="{{ route('user.accounts.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create New Account
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- All Accounts List -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">All My Accounts</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                                                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referral Code</th>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package Referrals</th>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($allAccounts as $account)
                        <tr class="hover:bg-gray-50 {{ $account->id === ($currentAccount->id ?? null) ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600">{{ substr($account->name, 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $account->name }}</div>
                                        @if($account->is_primary)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Primary
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-900">{{ $account->account_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-900">{{ $account->referral_code }}</div>
                            </td>
                                                         <td class="px-6 py-4 whitespace-nowrap">
                                 <div class="text-sm font-semibold text-gray-900">৳{{ number_format($account->total_balance, 2) }}</div>
                             </td>
                             <td class="px-6 py-4 whitespace-nowrap">
                                 <div class="text-sm font-semibold text-blue-600">{{ $account->purchase_referral_count }}</div>
                                 <div class="text-xs text-gray-500">Package Sales</div>
                             </td>
                             <td class="px-6 py-4 whitespace-nowrap">
                                 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                     {{ $account->status === 'active' ? 'bg-green-100 text-green-800' : 
                                        ($account->status === 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                     {{ ucfirst($account->status) }}
                                 </span>
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if($account->id !== ($currentAccount->id ?? null))
                                        <form action="{{ route('user.accounts.switch-account') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="sub_account_id" value="{{ $account->id }}">
                                            <button type="submit" class="text-blue-600 hover:text-blue-900">Switch</button>
                                        </form>
                                    @endif
                                    
                                    @if(!$account->is_primary)
                                        <form action="{{ route('user.accounts.set-primary', $account) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900">Set Primary</button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('user.accounts.show', $account) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    
                                    @if(!$account->is_primary && $account->total_balance == 0 && !$account->active_package_id)
                                        <form action="{{ route('user.accounts.destroy', $account) }}" method="POST" class="inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this account?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No accounts found. <a href="{{ route('user.accounts.create') }}" class="text-blue-600 hover:text-blue-900">Create your first account</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 flex justify-center space-x-4">
            <a href="{{ route('user.accounts.create') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create New Account
            </a>
            
            <a href="{{ route('user.accounts.switch') }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
                Switch Account
            </a>
        </div>
    </div>
</div>
@endsection
