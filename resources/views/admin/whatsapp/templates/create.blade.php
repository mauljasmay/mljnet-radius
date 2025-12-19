@extends('layouts.app')

@section('title', 'Buat Template WhatsApp')

@section('content')
<div class="min-h-screen bg-gray-100" x-data="{ sidebarOpen: false }">
    @include('admin.partials.sidebar')

    <div class="lg:pl-64">
        @include('admin.partials.topbar')

        <div class="p-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center">
                    <a href="{{ route('admin.whatsapp.templates.index') }}"
                       class="text-gray-500 hover:text-gray-700 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Buat Template WhatsApp</h1>
                        <p class="text-gray-600 mt-1">Buat template pesan WhatsApp baru</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900">Detail Template</h5>
                </div>

                <form action="{{ route('admin.whatsapp.templates.store') }}" method="POST" class="p-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Template <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   placeholder="Contoh: Invoice Notification" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipe Pesan <span class="text-red-500">*</span>
                            </label>
                            <select name="type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('type') border-red-500 @enderror" required>
                                <option value="">Pilih tipe pesan</option>
                                <option value="invoice" {{ old('type') == 'invoice' ? 'selected' : '' }}>Invoice</option>
                                <option value="reminder" {{ old('type') == 'reminder' ? 'selected' : '' }}>Pengingat Pembayaran</option>
                                <option value="suspension" {{ old('type') == 'suspension' ? 'selected' : '' }}>Penangguhan Layanan</option>
                                <option value="voucher" {{ old('type') == 'voucher' ? 'selected' : '' }}>Voucher</option>
                                <option value="custom" {{ old('type') == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Subjek (Opsional)
                            </label>
                            <input type="text" name="subject" value="{{ old('subject') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Contoh: Pemberitahuan Invoice">
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Is Default -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Jadikan template default untuk tipe ini</span>
                            </label>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Isi Pesan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="content" rows="8"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('content') border-red-500 @enderror"
                                  placeholder="Ketik isi pesan di sini..." required>{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            Gunakan variabel seperti {nama}, {invoice}, {amount}, dll. untuk konten dinamis.
                        </p>
                    </div>

                    <!-- Variables -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Variabel Tersedia (Opsional)
                        </label>
                        <input type="text" name="variables" value="{{ old('variables') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="nama,invoice,amount,due_date (pisahkan dengan koma)">
                        <p class="mt-1 text-sm text-gray-500">
                            Daftar variabel yang dapat digunakan dalam template, dipisahkan koma.
                        </p>
                        @error('variables')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preview -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Preview Pesan
                        </label>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div id="message-preview" class="text-sm whitespace-pre-wrap">
                                Ketik isi pesan untuk melihat preview...
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('admin.whatsapp.templates.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>Simpan Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.querySelector('textarea[name="content"]');
    const previewDiv = document.getElementById('message-preview');

    function updatePreview() {
        const content = contentTextarea.value;
        if (content.trim()) {
            // Simple preview - you might want to enhance this with actual variable replacement
            previewDiv.textContent = content;
        } else {
            previewDiv.textContent = 'Ketik isi pesan untuk melihat preview...';
        }
    }

    contentTextarea.addEventListener('input', updatePreview);
    updatePreview();
});
</script>
@endpush
@endsection
