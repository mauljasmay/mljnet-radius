# 🔐 MLJNET RADIUS - ISP Billing & Management System

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)
![Chart.js](https://img.shields.io/badge/Chart.js-4.x-FF6384?style=for-the-badge&logo=chartdotjs)

**MLJNET RADIUS** adalah sistem manajemen tagihan dan operasional ISP (Internet Service Provider) yang dibangun menggunakan **Laravel 12**. Sistem ini dirancang dengan antarmuka modern, analitik mendalam, dan fitur lengkap untuk mengelola bisnis ISP Anda.

---

## � Screenshots

<div align="center">
  <img src="img/login.png" alt="Login Page" width="45%">
  <img src="img/dashboard.png" alt="Dashboard" width="45%">
  <img src="img/customers.png" alt="Customer Management" width="45%">
  <img src="img/network-map.png" alt="Network Map" width="45%">
</div>

> **Note**: Screenshot aplikasi tersedia di folder `img/`

---

## ✨ Fitur Lengkap

### 🎨 **Modern UI/UX**
- **Theme ISP Network**: Desain modern dengan warna cyan & biru yang profesional
- **Responsive Design**: Tampilan optimal di desktop, tablet, dan mobile
- **Dark Sidebar**: Sidebar dengan gradient elegan dan navigasi intuitif
- **Interactive Charts**: Grafik analitik menggunakan Chart.js
- **Smooth Animations**: Transisi dan hover effects yang halus

### 📊 **Dashboard Analytics**
- **Real-time Statistics**: 
  - Total Customers & Active Status
  - Total Revenue & Pending Revenue
  - Package Distribution
  - Invoice Status
- **Interactive Charts**:
  - Revenue Trend (6 bulan terakhir)
  - Customer Growth Chart
  - Package Distribution (Doughnut Chart)
  - Invoice Status (Pie Chart)
- **Recent Activity**: Invoice dan customer terbaru
- **Quick Actions**: Akses cepat ke fitur utama

### 👥 **Customer Management**
- **CRUD Lengkap**: Create, Read, Update, Delete customer
- **Customer Profile**: Detail lengkap dengan statistik
- **Package Assignment**: Assign paket internet ke customer
- **Status Management**: Active, Inactive, Suspended
- **Search & Filter**: Pencarian dan filter berdasarkan status/paket
- **Invoice History**: Riwayat tagihan per customer

### 💰 **Invoice & Billing**
- **Auto Invoice Generation**: Generate invoice otomatis
- **Invoice Management**: Create, edit, view, print invoice
- **Payment Tracking**: Status paid/unpaid dengan tanggal bayar
- **Invoice Filtering**: Filter berdasarkan status, customer, tanggal
- **Professional Print**: Template invoice untuk print
- **Revenue Analytics**: Statistik pendapatan real-time

### 📦 **Package Management**
- **Flexible Packages**: Buat paket dengan harga dan kecepatan custom
- **Package Statistics**: Jumlah subscriber per paket
- **Tax Configuration**: Pengaturan pajak per paket
- **PPPoE Profile**: Mapping ke profil Mikrotik
- **Active/Inactive Status**: Kontrol paket yang ditampilkan

### 🎫 **Voucher System**
- **Voucher Purchase**: Sistem pembelian voucher online
- **Pricing Management**: Harga customer vs agen
- **Generation Settings**: Konfigurasi format voucher
- **Online Settings**: Durasi dan profil voucher
- **Delivery Logs**: Tracking pengiriman voucher
- **Sales Analytics**: Statistik penjualan voucher

### 🌐 **Network Management**
- **ODP Management**: Database Optical Distribution Point
- **Interactive Map**: Peta jaringan dengan Leaflet.js
- **Capacity Monitoring**: Visualisasi port usage
- **GPS Coordinates**: Lokasi ODP dengan koordinat
- **Status Tracking**: Active, Maintenance, Full
- **Cable Routes**: Manajemen rute kabel per customer
- **ONU Devices**: Database perangkat ONU
- **Network Segments**: Manajemen segmen jaringan
- **Maintenance Logs**: Riwayat maintenance infrastruktur

### 👨‍💼 **Agent System**
- **Agent Management**: CRUD agen penjualan
- **Balance System**: Manajemen saldo deposit agen
- **Transaction History**: Riwayat transaksi lengkap
- **Balance Requests**: Sistem request topup saldo
- **Voucher Sales**: Tracking penjualan voucher per agen
- **Commission System**: Perhitungan komisi otomatis
- **Monthly Payments**: Pembayaran bulanan via agen
- **Notifications**: Sistem notifikasi untuk agen

### 🛠️ **Staff Management**
- **Technicians**: Manajemen teknisi lapangan
- **Collectors**: Manajemen kolektor pembayaran
- **Area Coverage**: Pembagian area kerja
- **Performance Tracking**: Monitoring kinerja staff

### ⚙️ **System Settings**
- **Company Profile**: Konfigurasi data perusahaan
- **Payment Gateway**: Integrasi Midtrans/Xendit
- **WhatsApp Gateway**: Notifikasi otomatis via WA
- **Email Configuration**: Setup SMTP untuk email
- **System Preferences**: Pengaturan umum sistem

### 🔌 **Mikrotik Integration**
- **PPPoE Management**: Auto create/update/delete secrets, profile mapping, disconnect users
- **Hotspot Management**: User sessions, active connections, traffic monitoring
- **System Monitoring**: CPU, memory, uptime, interface statistics
- **Auto-sync**: Customer credentials sync with Mikrotik on create/update

### 📡 **GenieACS CPE Management**
- **Device Management**: List, view details, status monitoring (online/offline)
- **Remote Control**: Reboot, factory reset, refresh data, WiFi settings
- **Bulk Operations**: Bulk reboot, bulk refresh for multiple devices
- **TR-069 Protocol**: Full CWMP support for CPE provisioning

### 🛡️ **RADIUS Server Integration**
- **User Management**: Create, update, delete RADIUS users
- **Group/Profile**: Bandwidth profiles with rate limits
- **Session Monitoring**: Online users, session history (radacct)
- **CoA Support**: Disconnect and suspend/unsuspend users

### 📊 **SNMP Network Monitoring**
- **Device Monitoring**: System info, uptime, description
- **Traffic Statistics**: Interface in/out bandwidth (bps)
- **Resource Usage**: CPU and memory monitoring
- **Connectivity**: Ping and status checks

### 🔗 **CRM Integration**
- **Providers**: HubSpot, Salesforce, Zoho CRM
- **Features**: Contact sync, deal creation, activity logging
- **Bulk Sync**: Sync all customers to CRM

### 💼 **Accounting Integration**
- **Providers**: Accurate Online, Jurnal.id, Zahir
- **Features**: Customer sync, invoice sync, payment recording
- **Bulk Sync**: Sync all data to accounting software

---

## 🗄️ **Database Seeders**

Sistem dilengkapi dengan 23 seeder lengkap untuk data dummy:

### Core Data
- `UserSeeder` - Admin dan staff users
- `AppSettingSeeder` - Konfigurasi aplikasi
- `PackageSeeder` - Paket internet (10-100 Mbps)
- `VoucherPricingSeeder` - Harga voucher

### Staff & Agents
- `TechnicianSeeder` - Data teknisi
- `CollectorSeeder` - Data kolektor
- `AgentSeeder` - Data agen (3 agen)
- `AgentBalanceSeeder` - Saldo agen
- `AgentTransactionSeeder` - Transaksi agen
- `AgentBalanceRequestSeeder` - Request saldo
- `AgentNotificationSeeder` - Notifikasi agen
- `AgentPaymentSeeder` - Pembayaran via agen
- `AgentMonthlyPaymentSeeder` - Pembayaran bulanan
- `AgentVoucherSaleSeeder` - Penjualan voucher

### Network Infrastructure
- `OdpSeeder` - 5 ODP dengan koordinat GPS
- `NetworkSegmentSeeder` - Segmen jaringan
- `CableRouteSeeder` - Rute kabel customer
- `OnuDeviceSeeder` - Perangkat ONU
- `CableMaintenanceLogSeeder` - Log maintenance

### Customers & Billing
- `CustomerSeeder` - 5 customer dummy
- `InvoiceSeeder` - Invoice bulanan

### Voucher System
- `VoucherPurchaseSeeder` - 20 transaksi voucher
- `VoucherGenerationSettingSeeder` - Setting generator
- `VoucherOnlineSettingSeeder` - Setting online (1H-30D)
- `VoucherDeliveryLogSeeder` - Log pengiriman

### Reports
- `MonthlySummarySeeder` - Ringkasan 3 bulan terakhir

**Dokumentasi lengkap**: Lihat `database/seeders/README.md`

---

## 🚀 Instalasi & Setup

### ⚡ One-Click Automated Install (New!)

Untuk instalasi **sepenuhnya otomatis** tanpa interaksi pengguna di Ubuntu 22.04:

```bash
# One-liner installation (jalankan sebagai user biasa, bukan root)
curl -fsSL https://raw.githubusercontent.com/mauljasmay/mljnet-radius/main/auto-install-ubuntu.sh | bash
```

**Fitur Automated Install:**
- ✅ **Zero-interaction**: Tidak ada prompt atau input dari user
- ✅ **Sensible defaults**: URL `http://localhost:8000`, admin `admin@gembok.com`/`admin123`
- ✅ **Auto-generated passwords**: Password MySQL di-generate otomatis
- ✅ **Complete setup**: Install semua dependencies, setup database, build assets
- ✅ **Progress tracking**: Log detail setiap langkah instalasi
- ✅ **Error handling**: Stop otomatis jika ada error
- ✅ **Verification ready**: Siap untuk verifikasi instalasi

**Environment Variables (untuk customization):**
```bash
# Custom application URL
AUTO_INSTALL_APP_URL=https://myapp.com curl -fsSL https://raw.githubusercontent.com/mauljasmay/mljnet-radius/main/auto-install-ubuntu.sh | bash

# Custom admin credentials
AUTO_INSTALL_ADMIN_EMAIL=admin@example.com AUTO_INSTALL_ADMIN_PASS=mypassword curl -fsSL https://raw.githubusercontent.com/mauljasmay/mljnet-radius/main/auto-install-ubuntu.sh | bash

# Kombinasi lengkap
AUTO_INSTALL_APP_URL=https://myisp.com AUTO_INSTALL_ADMIN_EMAIL=admin@myisp.com AUTO_INSTALL_ADMIN_PASS=securepass curl -fsSL https://raw.githubusercontent.com/mauljasmay/mljnet-radius/main/auto-install-ubuntu.sh | bash
```

**Verifikasi Instalasi:**
```bash
# Jalankan script verifikasi setelah instalasi
cd mljnet-radius
./verify-installation.sh
```

**File yang dibuat:**
- `mljnet-radius/` - Direktori aplikasi
- `mysql_passwords.txt` - File berisi semua password yang di-generate

---

### ⚡ Quick Install (Interactive)

Untuk instalasi cepat dan otomatis di Ubuntu 22.04:

```bash
# Download dan jalankan script instalasi cepat
wget https://raw.githubusercontent.com/mauljasmay/mljnet-radius/main/quick-install-ubuntu.sh
chmod +x quick-install-ubuntu.sh
sudo ./quick-install-ubuntu.sh
```

**Fitur Quick Install:**
- ✅ Instalasi otomatis semua dependencies
- ✅ Setup database MySQL otomatis
- ✅ Konfigurasi environment otomatis
- ✅ Build assets dan setup permissions
- ✅ Progress indicator dan error handling
- ✅ Konfigurasi interaktif untuk custom settings

**Opsi Command Line:**
```bash
# Penggunaan dasar (password akan di-generate otomatis)
./quick-install-ubuntu.sh

# Dengan custom database password
./quick-install-ubuntu.sh -p mypassword

# Dengan custom URL dan password
./quick-install-ubuntu.sh -u https://myapp.com -p mypassword -r rootpassword

# Lihat semua opsi
./quick-install-ubuntu.sh --help
```

---

### 📋 Persyaratan Sistem

**Minimum Requirements:**
- **OS**: Ubuntu 22.04 LTS atau lebih tinggi
- **RAM**: 2GB (4GB recommended)
- **Storage**: 5GB free space
- **CPU**: 1 core (2 cores recommended)

**Software Requirements:**
- **PHP**: 8.2 atau 8.3
- **MySQL**: 8.0 atau MariaDB 10.6+
- **Node.js**: 18.x atau 20.x
- **Composer**: 2.x
- **Git**: 2.x

### 🐧 Manual Installation (Alternative)

Jika lebih suka instalasi manual step-by-step:

#### Step 1: Update Sistem
```bash
sudo apt update && sudo apt upgrade -y
```

#### Step 2: Install Dependencies Dasar
```bash
sudo apt install -y software-properties-common curl wget git unzip
```

#### Step 3: Install PHP 8.2
```bash
# Tambahkan repository PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.2 dan ekstensi yang dibutuhkan
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql \
php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath \
php8.2-gd php8.2-intl php8.2-tokenizer php8.2-fileinfo

# Verifikasi instalasi PHP
php --version
```

#### Step 4: Install MySQL 8.0
```bash
# Install MySQL Server
sudo apt install -y mysql-server

# Jalankan secure installation
sudo mysql_secure_installation

# Login ke MySQL dan buat database
sudo mysql -u root -p

# Di dalam MySQL shell:
CREATE DATABASE mljnet_radius CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mljnet_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON mljnet_radius.* TO 'mljnet_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Step 5: Install Node.js 20.x
```bash
# Install Node.js menggunakan NodeSource repository
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verifikasi instalasi
node --version
npm --version
```

#### Step 6: Install Composer
```bash
# Download dan install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Verifikasi instalasi
composer --version
```

#### Step 7: Clone dan Setup Project
```bash
# Clone repository
git clone https://github.com/mauljasmay/mljnet-radius.git
cd mljnet-radius

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env
```

#### Step 8: Konfigurasi Environment
```bash
# Generate application key
php artisan key:generate

# Edit file .env
nano .env
```

**Isi file .env:**
```env
APP_NAME="MLJ Net"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mljnet_radius
DB_USERNAME=mljnet_user
DB_PASSWORD=strong_password_here

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Queue (optional)
QUEUE_CONNECTION=database

# Mail Configuration (sesuaikan jika perlu)
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@mljnet.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### Step 9: Setup Database
```bash
# Jalankan migrasi dan seeding
php artisan migrate:fresh --seed

# Jika ada error, pastikan database credentials benar
```

#### Step 10: Build Assets
```bash
# Build untuk production
npm run build

# Atau untuk development
# npm run dev
```

#### Step 11: Setup Permissions
```bash
# Set permissions untuk Laravel
sudo chown -R www-data:www-data storage/
sudo chown -R www-data:www-data bootstrap/cache/
sudo chmod -R 775 storage/
sudo chmod -R 775 bootstrap/cache/
```

#### Step 12: Jalankan Aplikasi
```bash
# Untuk development/testing
php artisan serve

# Aplikasi akan berjalan di: http://localhost:8000
```

### 🐳 Alternatif: Menggunakan Docker

Jika lebih suka menggunakan Docker:

```bash
# Pastikan Docker dan Docker Compose terinstall
sudo apt install -y docker.io docker-compose

# Jalankan containers
docker-compose up -d

# Setup database di dalam container
docker-compose exec app php artisan migrate:fresh --seed

# Akses aplikasi di: http://localhost:8080
```

### 🔧 Troubleshooting

#### Error: "PHP extension missing"
```bash
# Install ekstensi yang missing
sudo apt install php8.2-[nama-ekstensi]
```

#### Error: "Permission denied"
```bash
# Set permissions yang benar
sudo chown -R $USER:$USER .
sudo chmod -R 775 storage/ bootstrap/cache/
```

#### Error: "Database connection failed"
- Pastikan MySQL service running: `sudo systemctl status mysql`
- Periksa credentials di `.env`
- Pastikan database dan user sudah dibuat

#### Error: "Composer memory limit"
```bash
# Increase memory limit
php -d memory_limit=-1 /usr/local/bin/composer install
```

### 📊 Verifikasi Instalasi

1. **Buka browser** dan akses `http://localhost:8000`
2. **Login admin** dengan:
   - Email: `admin@gembok.com`
   - Password: `admin123`
3. **Dashboard** harus muncul dengan data sample

### 🔄 Update Project

```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Run migrations jika ada
php artisan migrate

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 🔑 Akun Demo

| Role | Email | Password |
|------|-------|----------|
| **Administrator** | `admin@gembok.com` | `admin123` |

---

## 🛠️ Tech Stack

### Backend
- **Laravel 12** - PHP Framework
- **MySQL 8** - Database
- **Eloquent ORM** - Database abstraction

### Frontend
- **Blade Templates** - Templating engine
- **Tailwind CSS 3** - Utility-first CSS
- **Alpine.js** - Lightweight JavaScript
- **Chart.js 4** - Interactive charts
- **Leaflet.js** - Interactive maps
- **Font Awesome 6** - Icon library

### Tools & Libraries
- **Vite** - Frontend build tool
- **Composer** - PHP dependency manager
- **NPM** - JavaScript package manager

---

## 📁 Struktur Proyek

```
gembok-lara/
├── app/
│   ├── Http/Controllers/Admin/  # Controllers
│   ├── Models/                   # Eloquent Models
│   └── Providers/                # Service Providers
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── resources/
│   ├── views/admin/              # Blade templates
│   ├── css/                      # Stylesheets
│   └── js/                       # JavaScript
├── routes/
│   └── web.php                   # Route definitions
├── public/                       # Public assets
└── img/                          # Screenshots
```

---

## 🔒 Keamanan

MLJNET RADIUS dibangun dengan standar keamanan Laravel:

- ✅ **Authentication** - Session-based dengan Bcrypt hashing
- ✅ **CSRF Protection** - Token pada semua form
- ✅ **SQL Injection Protection** - Eloquent ORM binding
- ✅ **XSS Protection** - Blade auto-escaping
- ✅ **Input Validation** - Validasi ketat pada semua input
- ✅ **Password Hashing** - Bcrypt dengan salt
- ✅ **Secure Headers** - HTTP security headers

---

## 🗺️ Roadmap & Progress

### Phase 1 - Core System ✅ 100% Complete
| Feature | Status | Description |
|---------|--------|-------------|
| Customer Management | ✅ | CRUD, search, filter, status management |
| Package Management | ✅ | Pricing, bandwidth, PPPoE profile mapping |
| Invoice & Billing | ✅ | Auto-generate, print, payment tracking |
| Agent System | ✅ | Balance, transactions, voucher sales |
| Staff Management | ✅ | Technicians, collectors, area coverage |
| Voucher System | ✅ | Pricing, generation, online settings |
| Network Infrastructure | ✅ | ODP, cable routes, ONU devices |
| Analytics Dashboard | ✅ | Charts, statistics, real-time data |
| Modern UI/UX | ✅ | Tailwind CSS, responsive, dark sidebar |

### Phase 2 - Integration ✅ 100% Complete
| Feature | Status | Description |
|---------|--------|-------------|
| Mikrotik PPPoE | ✅ | Auto-sync secrets, profiles, disconnect |
| Mikrotik Hotspot | ✅ | User management, active sessions |
| GenieACS CPE | ✅ | TR-069, reboot, WiFi config, bulk ops |
| WhatsApp Gateway | ✅ | Fonnte/WaBlas, invoice notif, reminders |
| Payment Gateway | ✅ | Midtrans & Xendit, webhooks, auto-activate |
| Public Order System | ✅ | Package selection, payment, tracking |

### Phase 3 - Advanced Features ✅ 100% Complete
| Feature | Status | Description |
|---------|--------|-------------|
| Customer Portal | ✅ | Dashboard, invoices, payments, tickets, usage |
| Agent Portal | ✅ | Voucher sales, balance, transactions |
| Collector Portal | ✅ | Invoice collection, payment processing |
| Technician Portal | ✅ | Tasks, installations, repairs, map |
| API Documentation | ✅ | Customer & Admin REST API |
| Advanced Reporting | ✅ | Daily/monthly reports, multi-format export |
| Automated Billing | ✅ | Auto-generate, reminders, suspend, reactivate |
| Public Voucher Store | ✅ | Online purchase, WhatsApp delivery |

### Phase 4 - Enterprise Features ✅ 100% Complete
| Feature | Status | Description |
|---------|--------|-------------|
| RADIUS Server | ✅ | FreeRADIUS, user/group management, CoA |
| SNMP Monitoring | ✅ | Device status, traffic, CPU/memory |
| Ticketing System | ✅ | Categories, priorities, assignments |
| CRM Integration | ✅ | HubSpot, Salesforce, Zoho sync |
| Accounting Integration | ✅ | Accurate, Jurnal, Zahir sync |
| Multi-language | ✅ | English & Indonesian, language switcher |

### Phase 5 - Future Enhancements 📋 Planned
| Feature | Status | Description |
|---------|--------|-------------|
| Mobile App | 📋 | Flutter-based mobile application |
| Multi-tenant | 📋 | Support multiple ISP companies |
| SMS Gateway | 📋 | SMS notification integration |
| Email Marketing | 📋 | Promotional email campaigns |
| SLA Monitoring | 📋 | Service level agreement tracking |

---

## 📝 Changelog

### Version 1.2.0 (Current - December 2025)
- ✅ RADIUS Server Integration (FreeRADIUS)
- ✅ SNMP Network Monitoring
- ✅ CRM Integration (HubSpot/Salesforce/Zoho)
- ✅ Accounting Integration (Accurate/Jurnal/Zahir)
- ✅ Ticketing System with priorities & assignments
- ✅ Multi-language Support (EN/ID)
- ✅ Customer Portal (tickets, usage monitoring)
- ✅ Advanced Reporting (daily/monthly, CSV/JSON export)
- ✅ Automated Billing (auto-reactivate, WhatsApp reports)
- ✅ REST API with documentation

### Version 1.1.0 (November 2025)
- ✅ Mikrotik PPPoE & Hotspot Integration
- ✅ GenieACS CPE Management (TR-069)
- ✅ WhatsApp Gateway Integration
- ✅ Payment Gateway (Midtrans/Xendit)
- ✅ Multi-Portal System (Customer, Agent, Collector, Technician)
- ✅ Public Order & Voucher Store

### Version 1.0.0 (October 2025)
- ✅ Complete CRUD for all modules
- ✅ Modern UI with Cyan/Blue theme
- ✅ Interactive dashboard with Chart.js
- ✅ Network map with Leaflet.js
- ✅ 23 database seeders with realistic data
- ✅ Fully responsive design
- ✅ Print-ready invoice template
- ✅ Agent management system
- ✅ Voucher system
- ✅ ODP & network management
- ✅ Customer detail with statistics
- ✅ Revenue & growth analytics

---

## 🤝 Kontribusi

Kami sangat menghargai kontribusi Anda!

1. Fork repository
2. Buat branch baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

## 💬 Dukungan

- **Issues**: [GitHub Issues](https://github.com/mauljasmay/mljnet-radius/issues)
- **Discussions**: [GitHub Discussions](https://github.com/mauljasmay/mljnet-radius/discussions)

---

## ☕ Support Project

Jika proyek ini bermanfaat untuk Anda, pertimbangkan untuk memberikan dukungan:

<a href="https://saweria.co/rizkylab" target="_blank">
  <img src="https://img.shields.io/badge/Saweria-Support%20Me-orange?style=for-the-badge&logo=buy-me-a-coffee&logoColor=white" alt="Support via Saweria">
</a>

Dukungan Anda membantu pengembangan fitur baru dan maintenance proyek ini. Terima kasih! 🙏

---

## 📄 License

Proyek ini dilisensikan di bawah **MIT License**. Lihat file `LICENSE` untuk detail.

---

## 🙏 Acknowledgments

Proyek ini terinspirasi dari:
- **[Gembok Bill](https://github.com/alijayanet/gembok-bill)** oleh Ali Jaya Net

Terima kasih kepada:
- Laravel Community
- Tailwind CSS Team
- Chart.js Contributors
- Leaflet.js Team

---

## 📞 Contact

**Developer**: Maul Jasmay  
**Email**: mauljasmay2@gmail.com 
**GitHub**: [@mauljasmay](https://github.com/mauljasmay)

---

<div align="center">
  <strong>MLJNET RADIUS</strong> - <em>Simplifying ISP Management</em>
  <br><br>
  Made with ❤️ using Laravel & Tailwind CSS
</div>
