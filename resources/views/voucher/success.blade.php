<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembelian Voucher Berhasil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-slate-900 via-cyan-900 to-slate-900 min-h-screen">
    <!-- Header -->
    <header class="bg-white/10 backdrop-blur-lg border-b border-white/20">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wifi text-white"></i>
                </div>
                <span class="text-xl font-bold text-white">MLJ Net</span>
            </div>
            <a href="{{ route('customer.login') }}" class="text-cyan-400 hover:text-cyan-300">
                <i class="fas fa-user mr-1"></i> Login
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-2xl mx-auto px-4 py-12">
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl border border-white/20 p-8 text-center">
            <!-- Success Icon -->
            <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-green-400 text-3xl"></i>
            </div>

            <h1 class="text-3xl font-bold text-white mb-4">Pembelian Berhasil!</h1>
            <p class="text-cyan-200 mb-8">Terima kasih telah membeli voucher hotspot MLJ Net</p>

            <!-- Purchase Details -->
            <div class="bg-white/5 rounded-xl p-6 mb-8 text-left">
                <h2 class="text-xl font-semibold text-white mb-4">Detail Pembelian</h2>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-cyan-200">Nama:</span>
                        <span class="text-white">{{ $purchase->customer_name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-cyan-200">No. WhatsApp:</span>
                        <span class="text-white">{{ $purchase->customer_phone }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-cyan-200">Paket:</span>
                        <span class="text-white">{{ $purchase->voucher_package }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-cyan-200">Jumlah:</span>
                        <span class="text-white">Rp {{ number_format($purchase->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-cyan-200">Status:</span>
                        <span class="text-{{ $purchase->status == 'completed' ? 'green' : 'yellow' }}-400">
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-3">Langkah Selanjutnya:</h3>
                <ul class="text-cyan-200 text-sm space-y-2 text-left">
                    <li><i class="fas fa-check text-green-400 mr-2"></i> Kode voucher akan dikirim ke WhatsApp Anda</li>
                    <li><i class="fas fa-check text-green-400 mr-2"></i> Simpan kode voucher untuk login hotspot</li>
                    <li><i class="fas fa-check text-green-400 mr-2"></i> Gunakan username dan password yang diberikan</li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('voucher.buy') }}" class="bg-cyan-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-cyan-600 transition">
                    <i class="fas fa-plus mr-2"></i> Beli Lagi
                </a>
                <a href="{{ route('home') }}" class="bg-white/20 text-white px-6 py-3 rounded-lg font-semibold hover:bg-white/30 transition">
                    <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white/5 border-t border-white/10 mt-12">
        <div class="max-w-6xl mx-auto px-4 py-6 text-center text-cyan-300 text-sm">
            <p>&copy; {{ date('Y') }} MLJ Net. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
