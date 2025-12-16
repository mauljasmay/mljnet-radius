# 📦 Panduan Instalasi MLJNET RADIUS

## Pilihan Instalasi

- [Quick Install (Script Otomatis)](#quick-install)
- [Instalasi Manual (Tanpa Docker)](#instalasi-manual)
- [Instalasi dengan Docker](#instalasi-docker)

---

## Quick Install

### Ubuntu 22.04/24.04 (Nginx + MySQL + PHP 8.2)

```bash
# Download dan jalankan script
curl -fsSL https://raw.githubusercontent.com/rizkylab/gembok-lara/main/scripts/install-native.sh | sudo bash
```

Atau manual:

```bash
# Clone repo
git clone https://github.com/rizkylab/gembok-lara.git
cd gembok-lara

# Jalankan script
chmod +x scripts/install-native.sh
sudo ./scripts/install-native.sh
```

Script akan otomatis:
- Install Nginx, PHP 8.2, MySQL 8
- Buat database dan user
- Clone dan setup aplikasi
- Konfigurasi Nginx virtual host
- Setup Laravel scheduler cron

Setelah selesai, akses: `http://your-server-ip/admin/login`

---

## Instalasi Manual

### Prasyarat

- PHP >= 8.2
- Composer >= 2.0
- MySQL >= 8.0 atau MariaDB >= 10.4
- Node.js >= 18.x & NPM
- Git

### Langkah-langkah

#### 1. Clone Repository

```bash
git clone https://github.com/rizkylab/gembok-lara.git
cd gembok-lara
```

#### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

#### 3. Konfigurasi Environment

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 4. Edit File .env

Buka file `.env` dan sesuaikan konfigurasi:

```env
APP_NAME="MLJ Net"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gembok_lara
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Mikrotik (Opsional)
MIKROTIK_ENABLED=false
MIKROTIK_HOST=192.168.1.1
MIKROTIK_PORT=8728
MIKROTIK_USERNAME=admin
MIKROTIK_PASSWORD=

# GenieACS (Opsional)
GENIEACS_URL=http://localhost:7557
GENIEACS_USERNAME=
GENIEACS_PASSWORD=

# WhatsApp Gateway (Opsional)
WHATSAPP_API_URL=http://localhost:3000
WHATSAPP_API_KEY=
WHATSAPP_SENDER=

# Payment Gateway (Opsional)
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
```

#### 5. Setup Database

```bash
# Buat database
mysql -u root -p -e "CREATE DATABASE gembok_lara CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Jalankan migrasi
php artisan migrate

# (Opsional) Jalankan seeder untuk data dummy
php artisan db:seed
```

#### 6. Build Assets

```bash
# Production
npm run build

# Development (dengan hot reload)
npm run dev
```

#### 7. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 8. Jalankan Aplikasi

```bash
# Development
php artisan serve

# Production - gunakan web server (Nginx/Apache)
```

---

## Instalasi Docker

### Prasyarat

- Docker >= 20.x
- Docker Compose >= 2.x

### Langkah-langkah

#### 1. Clone Repository

```bash
git clone https://github.com/rizkylab/gembok-lara.git
cd gembok-lara
```

#### 2. Konfigurasi Environment

```bash
cp .env.example .env
```

Edit `.env` untuk Docker:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=gembok_lara
DB_USERNAME=gembok
DB_PASSWORD=secret

REDIS_HOST=redis
REDIS_PORT=6379
```

#### 3. Build & Jalankan Container

```bash
# Build dan jalankan
docker-compose up -d --build

# Lihat logs
docker-compose logs -f
```

#### 4. Setup Aplikasi

```bash
# Masuk ke container
docker-compose exec app bash

# Generate key
php artisan key:generate

# Jalankan migrasi
php artisan migrate

# (Opsional) Seed data
php artisan db:seed

# Build assets
npm install && npm run build
```

#### 5. Akses Aplikasi

- **Aplikasi**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081

### Docker Commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# Rebuild containers
docker-compose up -d --build

# View logs
docker-compose logs -f app

# Execute command in container
docker-compose exec app php artisan migrate

# Clear cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
```

---

## Konfigurasi Web Server

### Nginx

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/gembok-lara/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache (.htaccess sudah include)

Pastikan `mod_rewrite` aktif:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## Akun Default

| Role | Email | Password |
|------|-------|----------|
| Administrator | admin@gembok.com | admin123 |

**⚠️ PENTING**: Segera ganti password setelah login pertama!

---

## Troubleshooting

### Permission Error

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Composer Memory Limit

```bash
COMPOSER_MEMORY_LIMIT=-1 composer install
```

### Clear All Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Docker: Container tidak bisa connect ke database

Tunggu beberapa detik sampai MySQL ready, lalu:

```bash
docker-compose exec app php artisan migrate
```

---

## Support

Jika mengalami masalah, buka issue di:
- [GitHub Issues](https://github.com/rizkylab/gembok-lara/issues)

---

## ☕ Dukung Proyek Ini

<a href="https://saweria.co/rizkylab" target="_blank">
  <img src="https://img.shields.io/badge/Saweria-Support%20Me-orange?style=for-the-badge" alt="Support via Saweria">
</a>
