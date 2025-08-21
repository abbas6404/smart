@extends('users.layouts.app')

@section('title', 'Withdraw Commission')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Withdraw Commission</h1>
            <p class="text-gray-600 mt-2">Withdraw your earned commissions to your preferred payment method</p>
        </div>

        <!-- Available Balance Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="text-center">
                <div class="p-3 bg-green-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Available Balance</h2>
                <p class="text-4xl font-bold text-green-600">৳{{ number_format($availableBalance, 2) }}</p>
                <p class="text-sm text-gray-500 mt-2">Ready for withdrawal</p>
            </div>
        </div>

        @if($availableBalance > 0)
            <!-- Withdrawal Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Withdrawal Request</h3>
                
                <form action="{{ route('user.commissions.withdraw.process') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-6">
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Withdrawal Amount
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                                    ৳
                                </span>
                                <input type="number" 
                                       id="amount" 
                                       name="amount" 
                                       step="0.01" 
                                       min="10" 
                                       max="{{ $availableBalance }}"
                                       value="{{ old('amount', min(100, $availableBalance)) }}"
                                       class="block w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Enter amount">
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                Minimum: ৳10.00 | Maximum: ৳{{ number_format($availableBalance, 2) }}
                            </p>
                            @error('amount')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Method
                            </label>
                            <select id="payment_method" 
                                    name="payment_method" 
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select payment method</option>
                                <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="crypto" {{ old('payment_method') == 'crypto' ? 'selected' : '' }}>Cryptocurrency</option>
                            </select>
                            @error('payment_method')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="payment_details" class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Details
                            </label>
                            <textarea id="payment_details" 
                                      name="payment_details" 
                                      rows="4"
                                      class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Enter your payment details (account number, email, wallet address, etc.)">{{ old('payment_details') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">
                                Provide the necessary details for your selected payment method
                            </p>
                            @error('payment_details')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Important Notes</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Withdrawal requests are processed within 24-48 hours</li>
                                            <li>A small processing fee may apply</li>
                                            <li>Ensure your payment details are accurate</li>
                                            <li>Contact support if you have any questions</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('user.commissions.index') }}" 
                               class="text-indigo-600 hover:text-indigo-500 font-medium">
                                ← Back to Commissions
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Submit Withdrawal Request
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <!-- No Balance Message -->
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <div class="p-3 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Balance Available</h3>
                <p class="text-gray-500 mb-6">You don't have any approved commissions available for withdrawal.</p>
                <div class="space-x-4">
                    <a href="{{ route('user.commissions.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        View Commissions
                    </a>
                    <a href="{{ route('user.referrals.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Invite Referrals
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
