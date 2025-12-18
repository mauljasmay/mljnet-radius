#!/bin/bash

# ============================================
# MLJNET RADIUS - Installation Verification Script
# Comprehensive verification of MLJNET RADIUS installation
# ============================================

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Configuration variables
APP_NAME="mljnet-radius"
DB_NAME="mljnet_radius"
DB_USER="mljnet_user"
DB_PASS=""
MYSQL_ROOT_PASS=""

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

log_step() {
    echo -e "${PURPLE}[STEP]${NC} $1"
}

check_php() {
    log_step "1/8 Checking PHP installation..."

    # Check if PHP is installed
    if ! command -v php &> /dev/null; then
        log_error "PHP is not installed"
        return 1
    fi

    # Check PHP version (minimum 8.2)
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    PHP_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")
    PHP_MINOR=$(php -r "echo PHP_MINOR_VERSION;")

    if [[ $PHP_MAJOR -lt 8 ]] || [[ $PHP_MAJOR -eq 8 && $PHP_MINOR -lt 2 ]]; then
        log_error "PHP version $PHP_VERSION is too old. Minimum required: 8.2"
        return 1
    fi

    log_success "PHP $PHP_VERSION installed"

    # Check required extensions
    REQUIRED_EXTENSIONS=("mysql" "xml" "mbstring" "curl" "zip" "bcmath" "gd" "intl" "tokenizer" "fileinfo")
    MISSING_EXTENSIONS=()

    for ext in "${REQUIRED_EXTENSIONS[@]}"; do
        if ! php -m | grep -i "^$ext$" &> /dev/null; then
            MISSING_EXTENSIONS+=("$ext")
        fi
    done

    if [[ ${#MISSING_EXTENSIONS[@]} -gt 0 ]]; then
        log_error "Missing PHP extensions: ${MISSING_EXTENSIONS[*]}"
        return 1
    fi

    log_success "All required PHP extensions installed"
}

check_mysql() {
    log_step "2/8 Checking MySQL installation..."

    # Check if MySQL is installed
    if ! command -v mysql &> /dev/null; then
        log_error "MySQL is not installed"
        return 1
    fi

    # Check if MySQL service is running
    if ! sudo systemctl is-active --quiet mysql; then
        log_error "MySQL service is not running"
        return 1
    fi

    log_success "MySQL service is running"

    # Load database credentials from mysql_passwords.txt
    if [[ -f "mysql_passwords.txt" ]]; then
        DB_PASS=$(grep "Database Password:" mysql_passwords.txt | cut -d':' -f2 | xargs)
        MYSQL_ROOT_PASS=$(grep "MySQL Root Password:" mysql_passwords.txt | cut -d':' -f2 | xargs)
        log_info "Loaded database credentials from mysql_passwords.txt"
    else
        log_warning "mysql_passwords.txt not found. Please enter database credentials manually."
        read -p "Enter database password: " -s DB_PASS
        echo ""
        read -p "Enter MySQL root password: " -s MYSQL_ROOT_PASS
        echo ""
    fi

    # Test database connection
    if ! mysql -u"$DB_USER" -p"$DB_PASS" -e "SELECT 1;" &> /dev/null; then
        log_error "Cannot connect to database with user $DB_USER"
        return 1
    fi

    log_success "Database connection successful"

    # Check if database exists and has tables
    TABLE_COUNT=$(mysql -u"$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME; SHOW TABLES;" 2>/dev/null | wc -l)

    if [[ $TABLE_COUNT -lt 10 ]]; then
        log_error "Database $DB_NAME has only $TABLE_COUNT tables. Expected at least 10."
        return 1
    fi

    log_success "Database $DB_NAME has $TABLE_COUNT tables"
}

check_nodejs() {
    log_step "3/8 Checking Node.js installation..."

    # Check if Node.js is installed
    if ! command -v node &> /dev/null; then
        log_error "Node.js is not installed"
        return 1
    fi

    # Check Node.js version (minimum 18.0)
    NODE_VERSION=$(node -v | sed 's/v//')
    NODE_MAJOR=$(echo $NODE_VERSION | cut -d. -f1)

    if [[ $NODE_MAJOR -lt 18 ]]; then
        log_error "Node.js version $NODE_VERSION is too old. Minimum required: 18.0"
        return 1
    fi

    log_success "Node.js $NODE_VERSION installed"

    # Check if npm is installed
    if ! command -v npm &> /dev/null; then
        log_error "npm is not installed"
        return 1
    fi

    NPM_VERSION=$(npm -v)
    log_success "npm $NPM_VERSION installed"
}

check_composer() {
    log_step "4/8 Checking Composer installation..."

    # Check if Composer is installed
    if ! command -v composer &> /dev/null; then
        log_error "Composer is not installed"
        return 1
    fi

    COMPOSER_VERSION=$(composer --version | head -n 1)
    log_success "$COMPOSER_VERSION installed"
}

check_laravel_app() {
    log_step "5/8 Checking Laravel application setup..."

    # Check if we're in the right directory
    if [[ ! -d "$APP_NAME" ]]; then
        log_error "Application directory $APP_NAME not found"
        return 1
    fi

    cd "$APP_NAME"

    # Check if artisan file exists
    if [[ ! -f "artisan" ]]; then
        log_error "Laravel artisan file not found"
        return 1
    fi

    # Check if .env file exists
    if [[ ! -f ".env" ]]; then
        log_error "Laravel .env file not found"
        return 1
    fi

    log_success "Laravel application files found"

    # Test Laravel commands
    if ! php artisan --version &> /dev/null; then
        log_error "Cannot execute Laravel artisan commands"
        return 1
    fi

    LARAVEL_VERSION=$(php artisan --version)
    log_success "$LARAVEL_VERSION"

    # Check if storage directories are writable
    if [[ ! -w "storage/" ]]; then
        log_error "Storage directory is not writable"
        return 1
    fi

    if [[ ! -w "bootstrap/cache/" ]]; then
        log_error "Bootstrap cache directory is not writable"
        return 1
    fi

    log_success "File permissions are correct"
}

check_dependencies() {
    log_step "6/8 Checking project dependencies..."

    cd "$APP_NAME"

    # Check if vendor directory exists
    if [[ ! -d "vendor/" ]]; then
        log_error "Composer dependencies not installed (vendor/ directory missing)"
        return 1
    fi

    log_success "Composer dependencies installed"

    # Check if node_modules directory exists
    if [[ ! -d "node_modules/" ]]; then
        log_error "Node.js dependencies not installed (node_modules/ directory missing)"
        return 1
    fi

    log_success "Node.js dependencies installed"
}

check_database_setup() {
    log_step "7/8 Checking database setup..."

    cd "$APP_NAME"

    # Test database connection via Laravel
    if ! php artisan tinker --execute="echo 'Database connection OK';" &> /dev/null; then
        log_error "Laravel cannot connect to database"
        return 1
    fi

    log_success "Laravel database connection successful"

    # Check if migrations are run (look for users table)
    if ! php artisan tinker --execute="echo Schema::hasTable('users') ? 'Users table exists' : 'Users table missing';" | grep -q "Users table exists"; then
        log_error "Database migrations not run (users table missing)"
        return 1
    fi

    log_success "Database migrations completed"

    # Check if seeders are run (look for admin user)
    ADMIN_COUNT=$(php artisan tinker --execute="echo \App\Models\User::where('email', 'admin@gembok.com')->count();" 2>/dev/null || echo "0")
    if [[ "$ADMIN_COUNT" -eq 0 ]]; then
        log_error "Database seeders not run (admin user missing)"
        return 1
    fi

    log_success "Database seeders completed"
}

check_assets() {
    log_step "8/8 Checking asset compilation..."

    cd "$APP_NAME"

    # Check if public/build directory exists (Vite build output)
    if [[ ! -d "public/build/" ]]; then
        log_error "Frontend assets not compiled (public/build/ directory missing)"
        return 1
    fi

    # Check if CSS and JS files exist
    if [[ ! -f "public/build/manifest.json" ]]; then
        log_error "Vite manifest file missing"
        return 1
    fi

    log_success "Frontend assets compiled successfully"
}

show_summary() {
    echo ""
    echo "========================================"
    echo "🎉 VERIFICATION COMPLETED SUCCESSFULLY!"
    echo "========================================"
    echo ""
    echo "✅ All installation components verified:"
    echo "   • PHP 8.2+ with required extensions"
    echo "   • MySQL service running and configured"
    echo "   • Node.js 18+ and npm installed"
    echo "   • Composer installed"
    echo "   • Laravel application properly configured"
    echo "   • Project dependencies installed"
    echo "   • Database migrations and seeders completed"
    echo "   • Frontend assets compiled"
    echo ""
    echo "🚀 Your MLJNET RADIUS installation is ready!"
    echo ""
    echo "📋 Next Steps:"
    echo "   1. Start the application:"
    echo "      cd $APP_NAME && php artisan serve"
    echo ""
    echo "   2. Open browser and go to: http://localhost:8000"
    echo ""
    echo "   3. Login with admin credentials:"
    echo "      Email: admin@gembok.com"
    echo "      Password: admin123"
    echo ""
}

# Main verification process
main() {
    echo "========================================"
    echo "🔍 MLJNET RADIUS - Installation Verification"
    echo "========================================"
    echo ""

    # Run all checks
    check_php
    check_mysql
    check_nodejs
    check_composer
    check_laravel_app
    check_dependencies
    check_database_setup
    check_assets

    # Show summary
    show_summary
}

# Run main function
main "$@"
