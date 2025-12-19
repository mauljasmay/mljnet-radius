@extends('layouts.app')

@section('title', 'WhatsApp Templates')

@section('content')
<div class="min-h-screen bg-gray-100" x-data="{ sidebarOpen: false }">
    @include('admin.partials.sidebar')

    <div class="lg:pl-64">
        @include('admin.partials.topbar')

        <div class="p-6">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">WhatsApp Templates</h1>
                    <p class="text-gray-600 mt-1">Kelola template pesan WhatsApp</p>
                </div>
                <div>
                    <a href="{{ route('admin.whatsapp.templates.create') }}"
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-plus mr-2"></i>Tambah Template
                    </a>
                </div>
            </div>

            <!-- Templates List -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900">Daftar Template</h5>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Template
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipe
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Default
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($templates as $template)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <i class="fab fa-whatsapp text-green-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $template->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ Str::limit($template->content, 50) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($template->type === 'invoice') bg-blue-100 text-blue-800
                                        @elseif($template->type === 'reminder') bg-yellow-100 text-yellow-800
                                        @elseif($template->type === 'suspension') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($template->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="toggleStatus({{ $template->id }})"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($template->is_default)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-star mr-1"></i>Default
                                        </span>
                                    @else
                                        <button onclick="setDefault({{ $template->id }})"
                                                class="text-gray-400 hover:text-yellow-500">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.whatsapp.templates.edit', $template) }}"
                                           class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$template->is_default)
                                        <button onclick="deleteTemplate({{ $template->id }}, '{{ $template->name }}')"
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fab fa-whatsapp text-4xl text-gray-300 mb-2"></i>
                                        <p>Belum ada template WhatsApp</p>
                                        <a href="{{ route('admin.whatsapp.templates.create') }}"
                                           class="mt-2 text-green-600 hover:text-green-800">
                                            Buat template pertama
                                        </a>
                                    </div>
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
function toggleStatus(templateId) {
    fetch(`{{ url('admin/whatsapp/templates') }}/${templateId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showToast('error', data.message || 'Gagal mengubah status template');
        }
    })
    .catch(error => {
        showToast('error', 'Terjadi kesalahan');
    });
}

function setDefault(templateId) {
    confirmAction('Jadikan template ini sebagai default?', () => {
        fetch(`{{ url('admin/whatsapp/templates') }}/${templateId}/set-default`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showToast('error', data.message || 'Gagal menjadikan template default');
            }
        })
        .catch(error => {
            showToast('error', 'Terjadi kesalahan');
        });
    });
}

function deleteTemplate(templateId, templateName) {
    confirmAction(`Hapus template "${templateName}"?`, () => {
        fetch(`{{ url('admin/whatsapp/templates') }}/${templateId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showToast('error', data.message || 'Gagal menghapus template');
            }
        })
        .catch(error => {
            showToast('error', 'Terjadi kesalahan');
        });
    });
}
</script>
@endpush
@endsection
