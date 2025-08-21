@extends('admins.layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">System Settings</h1>
        <button type="submit" form="settings-form" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Save Settings
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- General Settings -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">General Settings</h2>
            </div>
            
            <form id="settings-form" action="{{ route('admin.settings.update-general') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                        <input type="text" name="site_name" id="site_name" 
                               value="{{ $settings['site_name']->value ?? 'Smart MLM System' }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="site_description" class="block text-sm font-medium text-gray-700">Site Description</label>
                        <textarea name="site_description" id="site_description" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $settings['site_description']->value ?? '' }}</textarea>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" 
                               value="1" {{ ($settings['maintenance_mode']->value ?? false) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="maintenance_mode" class="ml-2 block text-sm text-gray-900">Maintenance Mode</label>
                    </div>
                </div>
            </form>
        </div>

        <!-- Commission Settings -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Commission Settings</h2>
            </div>
            
            <form action="{{ route('admin.settings.update-commission') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="sponsor_commission" class="block text-sm font-medium text-gray-700">Sponsor Commission (%)</label>
                        <input type="number" name="sponsor_commission" id="sponsor_commission" 
                               value="{{ $settings['sponsor_commission']->value ?? 10 }}" min="0" max="100" step="0.01"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="generation_commission" class="block text-sm font-medium text-gray-700">Generation Commission (%)</label>
                        <input type="number" name="generation_commission" id="generation_commission" 
                               value="{{ $settings['generation_commission']->value ?? 5 }}" min="0" max="100" step="0.01"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="max_generation_levels" class="block text-sm font-medium text-gray-700">Max Generation Levels</label>
                        <input type="number" name="max_generation_levels" id="max_generation_levels" 
                               value="{{ $settings['max_generation_levels']->value ?? 5 }}" min="1" max="10"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Update Commission Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Auto Board Settings -->
    <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Auto Board Settings</h2>
        </div>
        
        <form action="{{ route('admin.settings.update-auto-board') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="auto_board_min_amount" class="block text-sm font-medium text-gray-700">Minimum Amount</label>
                    <input type="number" name="auto_board_min_amount" id="auto_board_min_amount" 
                           value="{{ $settings['auto_board_min_amount']->value ?? 100 }}" min="0" step="0.01"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="auto_board_max_amount" class="block text-sm font-medium text-gray-700">Maximum Amount</label>
                    <input type="number" name="auto_board_max_amount" id="auto_board_max_amount" 
                           value="{{ $settings['auto_board_max_amount']->value ?? 10000 }}" min="0" step="0.01"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="auto_board_distribution_time" class="block text-sm font-medium text-gray-700">Distribution Time</label>
                    <input type="time" name="auto_board_distribution_time" id="auto_board_distribution_time" 
                           value="{{ $settings['auto_board_distribution_time']->value ?? '00:00' }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    Update Auto Board Settings
                </button>
            </div>
        </form>
    </div>

    <!-- System Information -->
    <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">System Information</h2>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-md font-medium text-gray-900 mb-4">Application Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Laravel Version:</span>
                            <span class="text-sm font-medium text-gray-900">{{ app()->version() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">PHP Version:</span>
                            <span class="text-sm font-medium text-gray-900">{{ phpversion() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Environment:</span>
                            <span class="text-sm font-medium text-gray-900">{{ app()->environment() }}</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-md font-medium text-gray-900 mb-4">Database Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Database:</span>
                            <span class="text-sm font-medium text-gray-900">{{ config('database.default') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Last Updated:</span>
                            <span class="text-sm font-medium text-gray-900">{{ now()->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
