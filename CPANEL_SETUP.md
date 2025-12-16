# Setup Panduan untuk cPanel Hosting

Panduan lengkap untuk menginstall dan mengkonfigurasi MLJNET RADIUS di hosting cPanel.

## Persyaratan

- PHP 8.2 atau lebih tinggi
- MySQL 8.0 atau MariaDB 10.6+
- cPanel hosting dengan akses SSH (opsional tapi direkomendasikan)
- Composer (untuk install dependencies)

## Langkah Instalasi

### 1. Upload Files

1. Upload semua file project ke public_html atau subdomain directory
2. Pastikan file `.env` tidak diupload (akan dibuat nanti)

### 2. Setup Database

1. Login ke cPanel
2. Buka **MySQL Databases**
3. Buat database baru (contoh: `mljnet_radius`)
4. Buat user database dengan password kuat
5. Assign user ke database dengan full privileges

### 3. Konfigurasi Environment

1. Rename file `.env.example` menjadi `.env`
2. Edit `.env` dengan konfigurasi berikut:

```env
APP_NAME="MLJ Net"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=mljnet_radius
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Mail Configuration (sesuaikan dengan SMTP provider)
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your_email@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@yourdomain.com
MAIL_FROM_NAME="MLJ Net"

# Queue Configuration
QUEUE_CONNECTION=database

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
```

### 4. Install Dependencies

Via SSH (direkomendasikan):

```bash
cd public_html
composer install --no-dev --optimize-autoloader
```

Atau via cPanel File Manager (kurang direkomendasikan):
- Gunakan terminal cPanel jika tersedia

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Setup Database

```bash
php artisan migrate --seed
```

### 7. Setup Storage Link

```bash
php artisan storage:link
```

### 8. Setup Permissions

```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### 9. Setup Cron Job (Opsional)

Untuk scheduler Laravel, tambahkan cron job di cPanel:

```
* * * * * php /home/username/public_html/artisan schedule:run >> /dev/null 2>&1
```

### 10. Setup Queue Worker (Opsional)

Jika menggunakan queue:

```bash
php artisan queue:work --sleep=3 --tries=3 --max-jobs=1000
```

Atau setup sebagai background process.

## 🔗 Setup Integrasi

### Mikrotik RouterOS Integration

1. **Setup di Mikrotik**:
   - Enable API service di Mikrotik
   - Buat user khusus untuk API dengan permission read/write
   - Whitelist IP cPanel hosting Anda

2. **Konfigurasi di Aplikasi**:
   - Login ke admin panel
   - Pergi ke **Settings > Integration > Mikrotik**
   - Isi:
     - Host: IP Mikrotik router
     - Port: 8728 (default)
     - Username: api_user
     - Password: api_password
   - Test koneksi

3. **Firewall Setup di cPanel**:
   - Jika diperlukan, whitelist IP Mikrotik di cPanel firewall

### GenieACS Integration

1. **Install GenieACS di Server Terpisah**:
   - GenieACS perlu server dedicated/VPS
   - Ikuti panduan di `INTEGRATION.md`

2. **Konfigurasi di Aplikasi**:
   - Login ke admin panel
   - Pergi ke **Settings > Integration > GenieACS**
   - Isi:
     - URL: http://genieacs-server:7557
     - Username: admin
     - Password: admin

3. **Cron Job untuk Sync**:
   - Tambahkan cron job setiap 5 menit:
     ```
     */5 * * * * php /home/username/public_html/artisan genieacs:sync >> /dev/null 2>&1
     ```

### WhatsApp Integration

1. **Setup WhatsApp Business API**:
   - Daftar di 360Dialog atau provider WhatsApp API
   - Dapatkan API key dan URL

2. **Konfigurasi di Aplikasi**:
   - Login ke admin panel
   - Pergi ke **Settings > Integration > WhatsApp**
   - Isi API credentials

3. **Webhook Setup**:
   - Set webhook URL di provider WhatsApp:
     ```
     https://yourdomain.com/api/whatsapp/webhook
     ```

### Payment Gateway Integration

1. **Setup Midtrans/Gopay**:
   - Daftar akun di Midtrans
   - Dapatkan Server Key dan Client Key

2. **Konfigurasi di Aplikasi**:
   - Login ke admin panel
   - Pergi ke **Settings > Integration > Payment Gateway**
   - Pilih provider dan isi credentials

3. **Webhook Setup**:
   - Set webhook URL di payment gateway:
     ```
     https://yourdomain.com/api/payment/webhook
     ```

### RADIUS Server Integration

1. **Install FreeRADIUS di Server Terpisah**:
   - Setup FreeRADIUS server
   - Konfigurasi database MySQL

2. **Konfigurasi di Aplikasi**:
   - Login ke admin panel
   - Pergi ke **Settings > Integration > RADIUS**
   - Isi server details

3. **Database Sync**:
   - Setup cron job untuk sync users:
     ```
     */10 * * * * php /home/username/public_html/artisan radius:sync >> /dev/null 2>&1
     ```

## Konfigurasi cPanel Tambahan

### PHP Settings

1. Buka **MultiPHP Manager** di cPanel
2. Pastikan domain menggunakan PHP 8.2+
3. Enable ekstensi: `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`

### SSL Certificate

1. Install SSL certificate via **Let's Encrypt SSL** atau **SSL/TLS**
2. Pastikan semua redirect ke HTTPS

### Backup

Setup backup otomatis di cPanel untuk database dan files.

## Troubleshooting

### Error 500

- Cek file `.env` ada dan konfigurasi benar
- Cek permissions storage dan bootstrap/cache
- Cek log Laravel di `storage/logs/`

### Database Connection Error

- Pastikan DB credentials di `.env` benar
- Pastikan database dan user sudah dibuat
- Cek hostname (biasanya `localhost`)

### Permission Denied

- Set permissions folder ke 755, files ke 644
- Untuk shared hosting, kadang perlu 777 untuk storage sementara

## Support

Jika ada masalah, cek dokumentasi lengkap di `README.md` atau buka issue di GitHub.