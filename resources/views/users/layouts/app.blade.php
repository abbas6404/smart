<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Smart MLM') }} - @yield('title', 'User Panel')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Additional CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .main-content {
            background-color: #f8fafc;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.75rem;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 0.75rem 0.75rem 0 0 !important;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-xl font-bold text-gray-900">Smart MLM</h1>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <span class="text-sm text-gray-700">Welcome, {{ Auth::user()->name ?? 'User' }}</span>
                        </div>
                        
                        <div class="relative">
                            <form method="POST" action="{{ route('user.logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-red-600 hover:text-red-900">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex">
            <!-- Sidebar -->
            <div class="sidebar w-64 p-4">
                <nav class="mt-8">
                    <div class="px-4 py-2 text-white text-sm font-semibold uppercase tracking-wider">
                        Dashboard
                    </div>
                    
                                         <a href="{{ route('user.dashboard.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('user.dashboard.*') ? 'active' : '' }}">
                         <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                         </svg>
                         Dashboard
                     </a>
                     
                     <a href="{{ route('user.profile.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('user.profile.*') ? 'active' : '' }}">
                         <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                         </svg>
                         Profile
                     </a>
                     
                     <a href="{{ route('user.packages.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('user.packages.*') ? 'active' : '' }}">
                         <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                         </svg>
                         Packages
                     </a>
                     
                     <a href="{{ route('user.referrals.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('user.referrals.*') ? 'active' : '' }}">
                         <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                         </svg>
                         Referrals
                     </a>
                     
                     <a href="{{ route('user.commissions.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('user.commissions.*') ? 'active' : '' }}">
                         <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                         </svg>
                         Commissions
                     </a>
                     
                     <a href="{{ route('user.auto-board.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('user.auto-board.*') ? 'active' : '' }}">
                         <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                         </svg>
                         Auto Board
                     </a>
                     
                     <a href="{{ route('user.transactions.index') }}" class="nav-link flex items-center px-4 py-3 text-sm rounded-lg mb-1 {{ request()->routeIs('user.transactions.*') ? 'active' : '' }}">
                         <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 00-2 2v2h2V7z"></path>
                         </svg>
                         Transactions
                     </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="main-content flex-1 p-8">
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

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
