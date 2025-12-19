<?php

namespace Database\Seeders;

use App\Models\WhatsAppTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WhatsAppTemplateSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Invoice Notification',
                'type' => 'invoice',
                'subject' => 'Pemberitahuan Invoice',
                'content' => "📄 *Pemberitahuan Invoice*\n\nHalo *{nama}*,\n\nInvoice baru telah dibuat untuk layanan Anda:\n\n📋 *Detail Invoice:*\n• Nomor Invoice: {invoice}\n• Paket: {paket}\n• Jumlah: Rp {amount}\n• Jatuh Tempo: {due_date}\n\nSilakan lakukan pembayaran sebelum tanggal jatuh tempo untuk menghindari penangguhan layanan.\n\n*{app_name}*",
                'variables' => ['nama', 'invoice', 'paket', 'amount', 'due_date', 'app_name'],
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Payment Reminder',
                'type' => 'reminder',
                'subject' => 'Pengingat Pembayaran',
                'content' => "⏰ *Pengingat Pembayaran*\n\nHalo *{nama}*,\n\nKami ingin mengingatkan Anda bahwa invoice berikut belum dibayar:\n\n📋 *Detail Invoice:*\n• Nomor Invoice: {invoice}\n• Jumlah: Rp {amount}\n• Jatuh Tempo: {due_date}\n\nMohon segera lakukan pembayaran untuk menghindari penangguhan layanan.\n\n*{app_name}*",
                'variables' => ['nama', 'invoice', 'amount', 'due_date', 'app_name'],
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Suspension Notice',
                'type' => 'suspension',
                'subject' => 'Pemberitahuan Penangguhan',
                'content' => "🚫 *Pemberitahuan Penangguhan Layanan*\n\nHalo *{nama}*,\n\nLayanan internet Anda telah ditangguhkan karena tunggakan pembayaran.\n\nSilakan hubungi kami atau lakukan pembayaran untuk mengaktifkan kembali layanan Anda.\n\n*{app_name}*",
                'variables' => ['nama', 'app_name'],
                'is_default' => true,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            WhatsAppTemplate::create($template);
        }
    }
}
