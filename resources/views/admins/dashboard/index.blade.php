@extends('admins.layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white admin-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                <p class="text-gray-600 mt-1">System overview and key metrics for {{ now()->format('F j, Y') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">System Status</p>
                <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                    Online
                </span>
            </div>
        </div>
    </div>

    <!-- Key Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white admin-card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalUsers ?? 0 }}</p>
                    <p class="text-xs text-green-600">+{{ $newUsersThisMonth ?? 0 }} this month</p>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white admin-card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">৳{{ number_format($totalRevenue ?? 0, 2) }}</p>
                    <p class="text-xs text-green-600">+৳{{ number_format($revenueThisMonth ?? 0, 2) }} this month</p>
                </div>
            </div>
        </div>

        <!-- Active Packages -->
        <div class="bg-white admin-card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Packages</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activePackages ?? 0 }}</p>
                    <p class="text-xs text-blue-600">{{ $totalPackages ?? 0 }} total packages</p>
                </div>
            </div>
        </div>

        <!-- Pending Withdrawals -->
        <div class="bg-white admin-card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 13l5 5m0 0l5-5m-5 5V6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending Withdrawals</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingWithdrawals ?? 0 }}</p>
                    <p class="text-xs text-orange-600">৳{{ number_format($withdrawalAmount ?? 0, 2) }} total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto Board Status -->
    <div class="bg-white admin-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Auto Board System Status</h2>
            <div class="flex space-x-2">
                <span class="px-3 py-1 text-sm font-medium rounded-full 
                                    {{ $autoBoardStats['status'] === 'collection' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                {{ ucfirst($autoBoardStats['status'] ?? 'collection') }}
                </span>
                <button class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                    Manual Process
                </button>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Today's Collection</p>
                <p class="text-xl font-bold text-gray-900">৳{{ number_format($autoBoardStats['collection_amount'] ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Contributors</p>
                <p class="text-xl font-bold text-gray-900">{{ $autoBoardStats['contributors'] ?? 0 }}</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Eligible Accounts</p>
                <p class="text-xl font-bold text-gray-900">{{ $autoBoardStats['eligible_accounts'] ?? 0 }}</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Next Distribution</p>
                <p class="text-xl font-bold text-gray-900">{{ $autoBoardStats['next_distribution'] ?? '00:00' }}</p>
            </div>
        </div>
        
        @if($autoBoardStats['ready_for_distribution'] ?? false)
            <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800">
                    <strong>Ready for Distribution!</strong> Today's collection will be automatically distributed at midnight.
                </p>
            </div>
        @endif
    </div>

    <!-- Recent Activity & Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Transactions -->
        <div class="bg-white admin-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Recent Transactions</h2>
                <a href="{{ route('admin.transactions.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            
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
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $transaction->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
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

        <!-- System Alerts -->
        <div class="bg-white admin-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">System Alerts</h2>
                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">{{ $alertsCount ?? 0 }} alerts</span>
            </div>
            
            @if(isset($systemAlerts) && $systemAlerts->count() > 0)
                <div class="space-y-3">
                    @foreach($systemAlerts->take(5) as $alert)
                    <div class="p-3 border-l-4 border-{{ $alert->level === 'high' ? 'red' : ($alert->level === 'medium' ? 'yellow' : 'blue') }}-500 bg-{{ $alert->level === 'high' ? 'red' : ($alert->level === 'medium' ? 'yellow' : 'blue') }}-50 rounded">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-{{ $alert->level === 'high' ? 'red' : ($alert->level === 'medium' ? 'yellow' : 'blue') }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-{{ $alert->level === 'high' ? 'red' : ($alert->level === 'medium' ? 'yellow' : 'blue') }}-800">
                                    {{ $alert->title }}
                                </p>
                                <p class="text-sm text-{{ $alert->level === 'high' ? 'red' : ($alert->level === 'medium' ? 'yellow' : 'blue') }}-700 mt-1">
                                    {{ $alert->message }}
                                </p>
                                <p class="text-xs text-{{ $alert->level === 'high' ? 'red' : ($alert->level === 'medium' ? 'yellow' : 'blue') }}-600 mt-1">
                                    {{ $alert->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">All systems operational</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white admin-card p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.users.create') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <p class="font-medium text-gray-900">Add User</p>
                <p class="text-sm text-gray-500 text-center">Create new user account</p>
            </a>

            <a href="{{ route('admin.packages.create') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <p class="font-medium text-gray-900">New Package</p>
                <p class="text-sm text-gray-500 text-center">Create investment package</p>
            </a>

            <a href="{{ route('admin.withdrawals.index') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 13l5 5m0 0l5-5m-5 5V6"></path>
                    </svg>
                </div>
                <p class="font-medium text-gray-900">Process Withdrawals</p>
                <p class="text-sm text-gray-500 text-center">Review pending requests</p>
            </a>

            <a href="{{ route('admin.settings.index') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94-1.543-.826-3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <p class="font-medium text-gray-900">System Settings</p>
                <p class="text-sm text-gray-500 text-center">Configure MLM parameters</p>
            </a>
        </div>
    </div>
</div>
@endsection
