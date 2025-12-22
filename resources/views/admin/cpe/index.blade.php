@extends('layouts.app')

@section('title', 'CPE Management')

@section('content')
<div class="min-h-screen bg-gray-100" x-data="{ sidebarOpen: false, selectedDevices: [] }">
    @include('admin.partials.sidebar')

    <div class="lg:pl-64">
        @include('admin.partials.topbar')

        <div class="p-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">CPE Management</h1>
                        <p class="text-gray-600 mt-1">Remote manage customer modems via GenieACS</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($connected ?? false)
                            <span class="flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-lg">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                GenieACS Connected
                            </span>
                        @else
                            <span class="flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-lg">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                Disconnected
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            @if(!($connected ?? false))
                <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-4"></i>
                        <div>
                            <h3 class="text-lg font-bold text-red-800">Connection Failed</h3>
                            <p class="text-red-700">{{ $error ?? 'Unable to connect to GenieACS. Please check your configuration.' }}</p>
                            <p class="text-red-700 mt-2">You can configure GenieACS in <a href="{{ route('admin.settings.integrations') }}" class="underline">Settings > Integrations</a></p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-cyan-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Total Devices</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        </div>
                        <div class="h-14 w-14 bg-cyan-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-router text-cyan-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Online</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['online'] ?? 0 }}</p>
                        </div>
                        <div class="h-14 w-14 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Offline</p>
                            <p class="text-3xl font-bold text-red-600">{{ $stats['offline'] ?? 0 }}</p>
                        </div>
                        <div class="h-14 w-14 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            @if($connected ?? false)
                <!-- Search & Bulk Actions -->
                <div class="bg-white rounded-xl shadow-md p-4 mb-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <form method="GET" class="flex items-center space-x-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by device ID..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 w-64">
                            <button type="submit" class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 transition">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>

                        <div class="flex items-center space-x-2">
                            <button onclick="bulkRefresh()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition" x-show="selectedDevices.length > 0">
                                <i class="fas fa-sync-alt mr-2"></i>Bulk Refresh
                            </button>
                            <button onclick="bulkReboot()" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition" x-show="selectedDevices.length > 0">
                                <i class="fas fa-redo mr-2"></i>Bulk Reboot
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Devices Table -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                @if($connected ?? false)
                                    <th class="px-4 py-3 text-left">
                                        <input type="checkbox" @change="selectedDevices = $event.target.checked ? {{ json_encode($devices->pluck('id')) }} : []" class="rounded">
                                    </th>
                                @endif
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Device Info</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Connection</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">PPPoE</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                @if($connected ?? false)
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($devices ?? [] as $device)
                                <tr class="hover:bg-gray-50">
                                    @if($connected ?? false)
                                        <td class="px-4 py-3">
                                            <input type="checkbox" :value="'{{ $device['id'] }}'" x-model="selectedDevices" class="rounded">
                                        </td>
                                    @endif
                                    <td class="px-4 py-3">
                                        <div class="space-y-1">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $device['serial'] ?? 'Unknown' }}</p>
                                                <p class="text-xs text-gray-500 font-mono">{{ Str::limit($device['id'], 30) }}</p>
                                            </div>
                                            <div class="text-xs text-gray-600">
                                                <p><strong>Model:</strong> {{ $device['model'] ?? 'Unknown' }}</p>
                                                <p><strong>Manufacturer:</strong> {{ $device['manufacturer'] ?? 'Unknown' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="space-y-2">
                                            <div class="text-sm">
                                                <p class="font-medium text-gray-900">{{ $device['ip_address'] ?? '-' }}</p>
                                                <p class="text-xs text-gray-500">IP Address</p>
                                            </div>
                                            <div class="text-sm">
                                                <p class="font-medium text-gray-900">{{ $device['rx_power'] ?? '-' }}</p>
                                                <p class="text-xs text-gray-500">RX Power</p>
                                            </div>
                                            <div class="text-xs text-gray-600">
                                                <p><strong>Last Seen:</strong> {{ $device['last_inform'] }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="space-y-2">
                                            <div class="text-sm">
                                                <p class="font-medium">{{ $device['pppoe']['username'] ?? '-' }}</p>
                                                @if($device['pppoe']['password'] ?? null)
                                                    <p class="text-xs text-gray-500">••••••••</p>
                                                @endif
                                            </div>
                                            @if($device['pppoe']['connection_status'] ?? null)
                                                @if(strtolower($device['pppoe']['connection_status']) === 'connected')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                                        Connected
                                                    </span>
                                                @elseif(strtolower($device['pppoe']['connection_status']) === 'connecting')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5 animate-pulse"></span>
                                                        Connecting
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                                        {{ $device['pppoe']['connection_status'] }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($device['status'] === 'online')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                                Online
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                                Offline
                                            </span>
                                        @endif
                                    </td>
                                    @if($connected ?? false)
                                        <td class="px-4 py-3">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.cpe.show', urlencode($device['id'])) }}" class="text-cyan-600 hover:text-cyan-800" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button onclick="refreshDevice('{{ $device['id'] }}')" class="text-blue-600 hover:text-blue-800" title="Refresh">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                                <button onclick="rebootDevice('{{ $device['id'] }}')" class="text-yellow-600 hover:text-yellow-800" title="Reboot">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ ($connected ?? false) ? 5 : 4 }}" class="px-4 py-12 text-center">
                                        @if($connected ?? false)
                                            <i class="fas fa-router text-gray-300 text-5xl mb-4"></i>
                                            <p class="text-gray-500">No devices found</p>
                                        @else
                                            <i class="fas fa-cogs text-gray-300 text-5xl mb-4"></i>
                                            <p class="text-gray-500">GenieACS not configured</p>
                                            <p class="text-sm text-gray-400 mt-2">Configure GenieACS in Settings > Integrations to view devices</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto refresh every 60 seconds
setInterval(function() {
    location.reload();
}, 60000);

function refreshDevice(deviceId) {
    fetch(`/admin/cpe/${encodeURIComponent(deviceId)}/refresh`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    })
    .catch(error => alert('Error: ' + error.message));
}

function rebootDevice(deviceId) {
    if (!confirm('Are you sure you want to reboot this device?')) return;
    
    fetch(`/admin/cpe/${encodeURIComponent(deviceId)}/reboot`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    })
    .catch(error => alert('Error: ' + error.message));
}

function bulkRefresh() {
    const devices = Alpine.raw(Alpine.$data(document.querySelector('[x-data]')).selectedDevices);
    if (devices.length === 0) return;
    
    fetch('/admin/cpe/bulk-refresh', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ device_ids: devices })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        location.reload();
    });
}

function bulkReboot() {
    if (!confirm('Are you sure you want to reboot selected devices?')) return;
    
    const devices = Alpine.raw(Alpine.$data(document.querySelector('[x-data]')).selectedDevices);
    if (devices.length === 0) return;
    
    fetch('/admin/cpe/bulk-reboot', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ device_ids: devices })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        location.reload();
    });
}
</script>
@endpush
@endsection
