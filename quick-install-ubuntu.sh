#!/bin/bash

# ============================================
# MLJNET RADIUS - Quick Install Script for Ubuntu 22.04
# Automated installation for MLJNET RADIUS ISP Management System
# ============================================

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
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
APP_URL="http://localhost:8000"
ADMIN_EMAIL="admin@mljnet.com"
ADMIN_PASS="admin123"

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

check_root() {
    if [[ $EUID -eq 0 ]]; then
        log_error "This script should not be run as root. Please run as a regular user with sudo access."
        exit 1
    fi

    # Check if sudo is available
    if ! command -v sudo &> /dev/null; then
        log_error "sudo is required but not installed. Please install sudo first."
        exit 1
    fi
}

show_help() {
    echo "MLJNET RADIUS Quick Install Script for Ubuntu 22.04"
    echo ""
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  -p, --db-password PASS    Set MySQL database password"
    echo "  -r, --mysql-root PASS     Set MySQL root password"
    echo "  -u, --app-url URL         Set application URL (default: http://localhost:8000)"
    echo "  -h, --help                Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0                                    # Use default settings"
    echo "  $0 -p mypassword                     # Set database password"
    echo "  $0 -p mypassword -r rootpass         # Set both passwords"
    echo "  $0 -u http://myapp.com               # Set custom URL"
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -p|--db-password)
            DB_PASS="$2"
            shift 2
            ;;
        -r|--mysql-root)
            MYSQL_ROOT_PASS="$2"
            shift 2
            ;;
        -u|--app-url)
            APP_URL="$2"
            shift 2
            ;;
        -h|--help)
            show_help
            exit 0
            ;;
        *)
            log_error "Unknown option: $1"
            show_help
            exit 1
            ;;
    esac
done
        log_error "This script should not be run as root"
        exit 1
    fi
}

check_os() {
    if [[ ! -f /etc/os-release ]]; then
        log_error "Cannot detect OS. This script is designed for Ubuntu 22.04"
        exit 1
    fi

    . /etc/os-release
    if [[ "$ID" != "ubuntu" || "$VERSION_ID" != "22.04" ]]; then
        log_warning "This script is designed for Ubuntu 22.04. Detected: $PRETTY_NAME"
        read -p "Continue anyway? (y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi
}

update_system() {
    log_step "1/12 Updating system packages..."
    sudo apt update && sudo apt upgrade -y
    log_success "System updated"
}

install_basic_deps() {
    log_step "2/12 Installing basic dependencies..."
    sudo apt install -y software-properties-common curl wget git unzip ufw
    log_success "Basic dependencies installed"
}

install_php() {
    log_step "3/12 Installing PHP 8.2 and extensions..."
    # Add PHP repository
    sudo add-apt-repository ppa:ondrej/php -y
    sudo apt update

    # Install PHP 8.2 and required extensions
    sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql \
    php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath \
    php8.2-gd php8.2-intl php8.2-tokenizer php8.2-fileinfo

    # Verify PHP installation
    PHP_VERSION=$(php --version | head -n 1 | cut -d' ' -f2)
    log_success "PHP $PHP_VERSION installed"
}

install_mysql() {
    log_step "4/12 Installing MySQL 8.0..."

    # Check if MySQL is already installed
    if command -v mysql &> /dev/null; then
        log_warning "MySQL is already installed. Skipping installation."
        return 0
    fi

    # Generate passwords if not provided
    if [[ -z "$DB_PASS" ]]; then
        DB_PASS=$(openssl rand -base64 12)
        log_info "Generated database password: $DB_PASS"
    fi

    if [[ -z "$MYSQL_ROOT_PASS" ]]; then
        MYSQL_ROOT_PASS=$(openssl rand -base64 12)
        log_info "Generated MySQL root password: $MYSQL_ROOT_PASS"
    fi

    # Save passwords to a file for reference
    echo "MySQL Root Password: $MYSQL_ROOT_PASS" > mysql_passwords.txt
    echo "Database Password: $DB_PASS" >> mysql_passwords.txt
    echo "Database User: $DB_USER" >> mysql_passwords.txt
    echo "Database Name: $DB_NAME" >> mysql_passwords.txt
    log_info "Passwords saved to mysql_passwords.txt"

    # Install MySQL
    sudo apt install -y mysql-server

    # Stop MySQL service temporarily
    sudo systemctl stop mysql

    # Start MySQL in safe mode to set root password
    sudo mysqld_safe --skip-grant-tables --skip-networking &
    sleep 5

    # Set root password and create database/user
    sudo mysql -u root << EOF
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY '$MYSQL_ROOT_PASS';
CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF

    # Stop the safe mode MySQL
    sudo pkill mysqld
    sleep 2

    # Start MySQL service normally
    sudo systemctl start mysql
    sudo systemctl enable mysql

    # Test connection
    if mysql -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1;" &> /dev/null; then
        log_success "MySQL installed and configured successfully"
    else
        log_error "Failed to connect to MySQL with created user"
        exit 1
    fi
}

install_nodejs() {
    log_step "5/12 Installing Node.js 20.x..."
    # Install Node.js using NodeSource repository
    curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
    sudo apt-get install -y nodejs

    NODE_VERSION=$(node --version)
    NPM_VERSION=$(npm --version)
    log_success "Node.js $NODE_VERSION and npm $NPM_VERSION installed"
}

install_composer() {
    log_step "6/12 Installing Composer..."
    # Download and install Composer
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer

    COMPOSER_VERSION=$(composer --version | head -n 1)
    log_success "$COMPOSER_VERSION installed"
}

clone_project() {
    log_step "7/12 Cloning MLJNET RADIUS project..."
    if [[ -d "$APP_NAME" ]]; then
        log_warning "Directory $APP_NAME already exists. Removing..."
        rm -rf "$APP_NAME"
    fi

    git clone https://github.com/mauljasmay/mljnet-radius.git "$APP_NAME"
    cd "$APP_NAME"

    log_success "Project cloned successfully"
}

setup_environment() {
    log_step "8/12 Setting up environment..."
    cd "$APP_NAME"

    # Check if .env.example exists
    if [[ ! -f ".env.example" ]]; then
        log_error ".env.example file not found. Please ensure the project is properly cloned."
        exit 1
    fi

    # Copy environment file
    cp .env.example .env

    # Generate application key
    php artisan key:generate

    # Update .env file with proper escaping
    sed -i "s|APP_NAME=.*|APP_NAME=\"MLJ Net\"|" .env
    sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
    sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env
    sed -i "s|APP_URL=.*|APP_URL=\"$APP_URL\"|" .env
    sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=mysql|" .env
    sed -i "s|DB_HOST=.*|DB_HOST=127.0.0.1|" .env
    sed -i "s|DB_PORT=.*|DB_PORT=3306|" .env
    sed -i "s|DB_DATABASE=.*|DB_DATABASE=\"$DB_NAME\"|" .env
    sed -i "s|DB_USERNAME=.*|DB_USERNAME=\"$DB_USER\"|" .env
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=\"$DB_PASS\"|" .env

    log_success "Environment configured"
}

install_dependencies() {
    log_step "9/12 Installing project dependencies..."
    cd "$APP_NAME"

    # Install PHP dependencies
    composer install --no-dev --optimize-autoloader

    # Install Node.js dependencies
    npm install

    log_success "Dependencies installed"
}

setup_database() {
    log_step "10/12 Setting up database..."
    cd "$APP_NAME"

    # Run migrations and seeders
    php artisan migrate:fresh --seed

    log_success "Database setup completed"
}

build_assets() {
    log_step "11/12 Building frontend assets..."
    cd "$APP_NAME"

    # Build assets for production
    npm run build

    log_success "Assets built successfully"
}

setup_permissions() {
    log_step "12/12 Setting up permissions..."
    cd "$APP_NAME"

    # Set proper permissions
    sudo chown -R www-data:www-data storage/
    sudo chown -R www-data:www-data bootstrap/cache/
    sudo chmod -R 775 storage/
    sudo chmod -R 775 bootstrap/cache/

    log_success "Permissions configured"
}

show_completion() {
    echo ""
    echo "========================================"
    echo "🎉 INSTALLATION COMPLETED SUCCESSFULLY!"
    echo "========================================"
    echo ""
    echo "📊 Installation Summary:"
    echo "   • Application: MLJNET RADIUS"
    echo "   • URL: $APP_URL"
    echo "   • Database: $DB_NAME"
    echo "   • Database User: $DB_USER"
    echo "   • Admin Email: $ADMIN_EMAIL"
    echo "   • Admin Password: $ADMIN_PASS"
    echo ""
    echo "🔐 Important Passwords:"
    if [[ -f "mysql_passwords.txt" ]]; then
        echo "   • MySQL Root Password: $MYSQL_ROOT_PASS"
        echo "   • Database Password: $DB_PASS"
        echo "   • Passwords also saved to: mysql_passwords.txt"
    fi
    echo ""
    echo "🚀 Next Steps:"
    echo "   1. Start the application:"
    echo "      cd $APP_NAME && php artisan serve"
    echo ""
    echo "   2. Open browser and go to: $APP_URL"
    echo ""
    echo "   3. Login with admin credentials above"
    echo ""
    echo "📝 Additional Notes:"
    echo "   • Make sure to change the default admin password after first login"
    echo "   • Configure your web server (nginx/apache) for production use"
    echo "   • Set up SSL certificates for HTTPS"
    echo "   • Configure backup scripts for database and files"
    echo ""
}
    echo ""
    echo "   3. Login with admin credentials above"
    echo ""
    echo "🔧 Additional Commands:"
    echo "   • View logs: tail -f storage/logs/laravel.log"
    echo "   • Clear cache: php artisan cache:clear"
    echo "   • Run queue: php artisan queue:work"
    echo ""
    echo "📚 Documentation:"
    echo "   • README.md - Complete documentation"
    echo "   • CPANEL_SETUP.md - cPanel deployment"
    echo "   • INTEGRATION.md - Third-party integrations"
    echo ""
    echo "💡 Tips:"
    echo "   • For production, use a web server like Nginx or Apache"
    echo "   • Set up SSL certificate for HTTPS"
    echo "   • Configure cron jobs for scheduled tasks"
    echo "   • Set up backup system for database and files"
    echo ""
}

# Main installation process
main() {
    echo "========================================"
    echo "🚀 MLJNET RADIUS - Quick Install for Ubuntu 22.04"
    echo "========================================"
    echo ""

    # Pre-installation checks
    check_root
    check_os

    # Get user input for configuration
    read -p "Enter application URL [$APP_URL]: " input_url
    APP_URL=${input_url:-$APP_URL}

    read -p "Enter admin email [$ADMIN_EMAIL]: " input_email
    ADMIN_EMAIL=${input_email:-$ADMIN_EMAIL}

    read -p "Enter admin password [$ADMIN_PASS]: " -s input_pass
    echo ""
    ADMIN_PASS=${input_pass:-$ADMIN_PASS}

    read -p "Enter MySQL root password: " -s mysql_root_pass
    echo ""
    if [[ -n "$mysql_root_pass" ]]; then
        DB_PASS="$mysql_root_pass"
    fi

    echo ""
    log_info "Starting installation with the following configuration:"
    log_info "  • App URL: $APP_URL"
    log_info "  • Admin Email: $ADMIN_EMAIL"
    log_info "  • Database: $DB_NAME"
    echo ""

    read -p "Continue with installation? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        log_info "Installation cancelled."
        exit 0
    fi

    # Run installation steps
    update_system
    install_basic_deps
    install_php
    install_mysql
    install_nodejs
    install_composer
    clone_project
    setup_environment
    install_dependencies
    setup_database
    build_assets
    setup_permissions

    # Show completion message
    show_completion
}

# Handle command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --help|-h)
            echo "MLJNET RADIUS Quick Install Script for Ubuntu 22.04"
            echo ""
            echo "Usage: $0 [options]"
            echo ""
            echo "Options:"
            echo "  --help, -h          Show this help message"
            echo "  --url=URL           Set application URL (default: http://localhost:8000)"
            echo "  --email=EMAIL       Set admin email (default: admin@mljnet.com)"
            echo "  --password=PASS     Set admin password (default: admin123)"
            echo "  --db-pass=PASS      Set MySQL password"
            echo ""
            echo "Example:"
            echo "  $0 --url=https://myapp.com --email=admin@example.com"
            exit 0
            ;;
        --url=*)
            APP_URL="${1#*=}"
            shift
            ;;
        --email=*)
            ADMIN_EMAIL="${1#*=}"
            shift
            ;;
        --password=*)
            ADMIN_PASS="${1#*=}"
            shift
            ;;
        --db-pass=*)
            DB_PASS="${1#*=}"
            shift
            ;;
        *)
            log_error "Unknown option: $1"
            echo "Use --help for usage information"
            exit 1
            ;;
    esac
done

# Run main function
main "$@"