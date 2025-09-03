<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Smart Cash Club') }} - Admin Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Simple CSS -->
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .login-bg {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 50%, #7f1d1d 100%);
            background-size: 400% 400%;
            animation: dangerPulse 3s ease-in-out infinite;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        
        @keyframes dangerPulse {
            0% { 
                background-position: 0% 50%;
                box-shadow: inset 0 0 100px rgba(220, 38, 38, 0.3);
            }
            50% { 
                background-position: 100% 50%;
                box-shadow: inset 0 0 150px rgba(220, 38, 38, 0.5);
            }
            100% { 
                background-position: 0% 50%;
                box-shadow: inset 0 0 100px rgba(220, 38, 38, 0.3);
            }
        }
        
        .login-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 20%, rgba(255, 0, 0, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 0, 0, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 60%, rgba(255, 0, 0, 0.1) 0%, transparent 50%);
            animation: dangerFloat 4s ease-in-out infinite;
        }
        
        @keyframes dangerFloat {
            0%, 100% { 
                transform: translateY(0px) scale(1);
                opacity: 0.3;
            }
            50% { 
                transform: translateY(-20px) scale(1.1);
                opacity: 0.6;
            }
        }
        
        .warning-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255, 0, 0, 0.1) 10px,
                rgba(255, 0, 0, 0.1) 20px
            );
            animation: warningStripes 2s linear infinite;
        }
        
        @keyframes warningStripes {
            0% { transform: translateX(0); }
            100% { transform: translateX(20px); }
        }
        
        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .login-input {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            transition: border-color 0.2s ease;
        }
        
        .login-input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .login-btn {
            background: #3b82f6;
            transition: background-color 0.2s ease;
            border-radius: 8px;
        }
        
        .login-btn:hover {
            background: #2563eb;
        }
        
        .admin-icon {
            background: linear-gradient(135deg, #dc2626, #991b1b);
            animation: dangerGlow 2s ease-in-out infinite alternate;
        }
        
        @keyframes dangerGlow {
            0% { 
                box-shadow: 0 0 20px rgba(220, 38, 38, 0.5);
            }
            100% { 
                box-shadow: 0 0 30px rgba(220, 38, 38, 0.8);
            }
        }
        
        .warning-text {
            color: #dc2626;
            animation: warningBlink 1.5s ease-in-out infinite;
        }
        
        @keyframes warningBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body class="login-bg flex items-center justify-center min-h-screen p-4">
    <!-- Warning Overlay -->
    <div class="warning-overlay"></div>
    
    <div class="w-full max-w-md relative z-10">
        <!-- Login Card -->
        <div class="login-card p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <!-- Admin Icon -->
                <div class="inline-flex items-center justify-center w-16 h-16 admin-icon rounded-xl mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                
                <!-- Title -->
                <h1 class="text-2xl font-bold warning-text mb-2">
                    ⚠️ ADMIN ACCESS ONLY ⚠️
                </h1>
                <p class="text-red-600 text-sm font-medium">
                    RESTRICTED AREA - AUTHORIZED PERSONNEL ONLY
                </p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-6">
                @csrf

                <!-- Username/Email Field -->
                <div>
                    <label for="login" class="block text-sm font-medium text-gray-700 mb-2">
                        Username or Email
                    </label>
                    <input 
                        id="login" 
                        name="login" 
                        type="text" 
                        value="{{ old('login') }}"
                        class="login-input w-full px-4 py-3 text-gray-900 focus:outline-none @error('login') border-red-500 @enderror" 
                        placeholder="Enter your username or email"
                        required 
                        autofocus
                    >
                    @error('login')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        class="login-input w-full px-4 py-3 text-gray-900 focus:outline-none @error('password') border-red-500 @enderror" 
                        placeholder="Enter your password"
                        required
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="hidden" name="remember" value="0">
                    <input 
                        id="remember" 
                        name="remember" 
                        type="checkbox" 
                        value="1"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="login-btn w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Sign In
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    Need to access other areas? 
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                        User Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>