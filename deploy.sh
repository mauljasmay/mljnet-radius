#!/bin/bash

# ============================================
# MLJNET RADIUS - Deploy Script for cPanel
# Compatible with cPanel shared hosting
# ============================================

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PHP_VERSION="8.2"  # Change if needed
DOMAIN=""  # Will be set interactively

# Functions
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

check_requirements() {
    log_info "Checking system requirements..."

    # Check if we're in the right directory
    if [ ! -f "artisan" ]; then
        log_error "Please run this script from the Laravel project root directory"
        exit 1
    fi

    # Check PHP version
    if ! command -v php &> /dev/null; then
        log_error "PHP is not installed or not in PATH"
        exit 1
    fi

    PHP_VER=$(php -r "echo PHP_VERSION;")
    log_info "PHP Version: $PHP_VER"

    # Check if version is >= 8.2
    if ! php -r "exit(version_compare(PHP_VERSION, '8.2.0', '>=') ? 0 : 1)"; then
        log_error "PHP 8.2 or higher is required. Current version: $PHP_VER"
        exit 1
    fi

    # Check Composer
    if ! command -v composer &> /dev/null; then
        log_error "Composer is not installed or not in PATH"
        exit 1
    fi

    log_success "Requirements check passed"
}

setup_environment() {
    log_info "Setting up environment..."

    # Check if .env exists
    if [ ! -f ".env" ]; then
        if [ -f ".env.example" ]; then
            cp .env.example .env
            log_success ".env file created from .env.example"
        else
            log_error ".env.example file not found"
            exit 1
        fi
    else
        log_warning ".env file already exists, skipping creation"
    fi

    # Generate application key if not set
    if ! grep -q "APP_KEY=.*[^base64:]" .env; then
        log_info "Generating application key..."
        php artisan key:generate
        log_success "Application key generated"
    else
        log_warning "Application key already exists"
    fi
}

install_dependencies() {
    log_info "Installing PHP dependencies..."

    # Install composer dependencies
    composer install --no-dev --optimize-autoloader

    log_success "Dependencies installed"
}

setup_database() {
    log_info "Setting up database..."

    read -p "Enter database name: " DB_NAME
    read -p "Enter database username: " DB_USER
    read -s -p "Enter database password: " DB_PASS
    echo ""

    # Update .env file
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env

    log_info "Running database migrations..."
    php artisan migrate --seed

    log_success "Database setup completed"
}

setup_permissions() {
    log_info "Setting up file permissions..."

    # Set permissions for Laravel
    find . -type f -name "*.php" -exec chmod 644 {} \;
    find . -type d -exec chmod 755 {} \;

    # Storage permissions
    chmod -R 775 storage/
    chmod -R 775 bootstrap/cache/

    log_success "Permissions set"
}

setup_storage() {
    log_info "Setting up storage link..."

    php artisan storage:link

    log_success "Storage link created"
}

clear_cache() {
    log_info "Clearing and optimizing cache..."

    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    log_success "Cache optimized"
}

setup_domain() {
    log_info "Setting up domain configuration..."

    read -p "Enter your domain (e.g., https://yourdomain.com): " DOMAIN

    # Update APP_URL in .env
    sed -i "s|APP_URL=.*|APP_URL=$DOMAIN|" .env

    log_success "Domain configured: $DOMAIN"
}

create_admin_user() {
    log_info "Creating admin user..."

    read -p "Enter admin email: " ADMIN_EMAIL
    read -s -p "Enter admin password: " ADMIN_PASS
    echo ""

    # Check if user already exists
    USER_EXISTS=$(php artisan tinker --execute="echo \App\Models\User::where('email', '$ADMIN_EMAIL')->exists() ? 'yes' : 'no';")

    if [ "$USER_EXISTS" = "yes" ]; then
        log_warning "Admin user with email $ADMIN_EMAIL already exists"
    else
        # Create admin user
        php artisan tinker --execute="
        \App\Models\User::create([
            'name' => 'Administrator',
            'email' => '$ADMIN_EMAIL',
            'password' => \Illuminate\Support\Facades\Hash::make('$ADMIN_PASS'),
            'email_verified_at' => now(),
        ]);
        echo 'Admin user created successfully';
        "

        log_success "Admin user created: $ADMIN_EMAIL"
    fi
}

final_steps() {
    log_info "Final setup steps..."

    echo ""
    echo "========================================"
    echo "DEPLOYMENT COMPLETED SUCCESSFULLY!"
    echo "========================================"
    echo ""
    echo "Your application is now ready at: $DOMAIN"
    echo ""
    echo "Admin Panel: $DOMAIN/admin"
    echo "Admin Email: $ADMIN_EMAIL"
    echo ""
    echo "Next steps:"
    echo "1. Set up SSL certificate in cPanel"
    echo "2. Configure cron jobs for Laravel scheduler"
    echo "3. Set up integrations (Mikrotik, GenieACS, WhatsApp, etc.)"
    echo "4. Configure payment gateways"
    echo "5. Set up backup system"
    echo "6. Test the application thoroughly"
    echo ""
    echo "Integration Setup:"
    echo "- Mikrotik: Configure router API access"
    echo "- GenieACS: Setup TR-069 server (requires separate server)"
    echo "- WhatsApp: Configure business API"
    echo "- Payment: Setup Midtrans/Gopay integration"
    echo "- RADIUS: Configure FreeRADIUS server"
    echo ""
    echo "See CPANEL_SETUP.md for detailed integration instructions"
    echo ""
    echo "For security, you should:"
    echo "- Delete this deploy.sh file"
    echo "- Remove write permissions from .env file"
    echo "- Set up proper file permissions"
    echo ""
}

# Main deployment process
main() {
    echo "========================================"
    echo "MLJNET RADIUS - cPanel Deployment Script"
    echo "========================================"
    echo ""

    check_requirements
    setup_environment
    install_dependencies
    setup_domain
    setup_database
    create_admin_user
    setup_permissions
    setup_storage
    clear_cache
    final_steps
}

# Run main function
main "$@"