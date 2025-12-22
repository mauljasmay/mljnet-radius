@extends('admin.layout')

@section('title', 'Project Updates')

@section('content')
<div class="min-h-screen bg-gray-100" x-data="{ sidebarOpen: false }">
    @include('admin.partials.sidebar')
    @include('admin.partials.topbar')

    <div class="lg:pl-64">
        <div class="p-6">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Project Updates</h1>
                    <p class="text-gray-600 mt-1">Track the latest updates and version history for this project</p>
                </div>

                <!-- Current Version -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Current Version</h2>
                            <p class="text-3xl font-bold text-cyan-600 mt-2">{{ $currentVersion }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Last Updated</p>
                            <p class="text-sm font-medium text-gray-900">{{ now()->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Update History -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Update History</h2>

                    <div class="space-y-4">
                        @foreach($updates as $update)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 bg-cyan-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-code-branch text-cyan-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">Version {{ $update['version'] }}</h3>
                                        <p class="text-sm text-gray-500">{{ $update['date'] }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                    Released
                                </span>
                            </div>

                            <div class="ml-13">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Changes:</h4>
                                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                    @foreach($update['changes'] as $change)
                                    <li>{{ $change }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Version Management Note -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Version Management</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Versions are automatically updated when changes are pushed to GitHub. The current version is stored in the application settings.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
