<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Smart MLM') }} - Admin Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Additional CSS -->
    <style>
        .admin-sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        }
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            transition: all 0.3s ease;
        }
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .admin-content {
            background-color: #f1f5f9;
        }
        .admin-card {
            border: none;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
        }
        .admin-card-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <!-- Top Navigation -->
        <nav class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-xl font-bold text-gray-900">Smart MLM - Admin Panel</h1>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <span class="text-sm text-gray-700">Administrator</span>
                        </div>
                        
                        <div class="relative">
                            <a href="{{ route('user.login') }}" class="text-sm text-red-600 hover:text-red-900">
                                Go to User Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex">
            <!-- Admin Sidebar -->
            <div class="admin-sidebar w-64 p-4">
                <nav class="mt-8">
                    <div class="px-4 py-2 text-white text-sm font-semibold uppercase tracking-wider">
                        Administration
                    </div>
                    
                    <a href="{{ route('admin.dashboard.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Dashboard
                    </a>
                    
                    <div class="px-4 py-2 text-white text-sm font-semibold uppercase tracking-wider mt-6">
                        User Management
                    </div>
                    
                    <a href="{{ route('admin.users.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Users
                    </a>
                    
                    <a href="{{ route('admin.sub-accounts.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('admin.sub-accounts*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Sub Accounts
                    </a>
                    
                    <div class="px-4 py-2 text-white text-sm font-semibold uppercase tracking-wider mt-6">
                        Financial Management
                    </div>
                    
                    <a href="{{ route('admin.packages.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Packages
                    </a>
                    
                    <a href="{{ route('admin.transactions.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 00-2 2v2h2V7z"></path>
                        </svg>
                        Transactions
                    </a>
                    
                    <a href="{{ route('admin.withdrawals.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 13l5 5m0 0l5-5m-5 5V6"></path>
                        </svg>
                        Withdrawals
                    </a>
                    
                    <div class="px-4 py-2 text-white text-sm font-semibold uppercase tracking-wider mt-6">
                        MLM System
                    </div>
                    
                    <a href="{{ route('admin.auto-board.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('admin.auto-board.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Auto Board
                    </a>
                    
                    <a href="{{ route('admin.commissions.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('admin.commissions.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Commissions
                    </a>
                    
                    <div class="px-4 py-2 text-white text-sm font-semibold uppercase tracking-wider mt-6">
                        System Settings
                    </div>
                    
                    <a href="{{ route('admin.settings.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Settings
                    </a>
                    
                    <a href="{{ route('admin.reports.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Reports
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="admin-content flex-1 p-8">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                        {{ session('warning') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                        {{ session('info') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
