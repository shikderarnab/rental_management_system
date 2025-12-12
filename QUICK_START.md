# Quick Start Guide - XAMPP

## Quick Steps to Run the Project

### Step 1: Install XAMPP

If you don't have XAMPP installed:
1. Download: https://www.apachefriends.org/
2. Install it (default: `C:\xampp`)

### Step 2: Automated Setup (Recommended)

In PowerShell, navigate to project folder:

```powershell
.\setup-xampp.ps1
```

This script will automatically:
- Copy project to `C:\xampp\htdocs\rental-management`
- Install dependencies
- Setup configuration

### Step 3: Start XAMPP Services

In XAMPP Control Panel:
1. Click **Start** button for **Apache**
2. Click **Start** button for **MySQL**

### Step 4: Create Database

Go to phpMyAdmin: http://localhost/phpmyadmin

1. Click "New" in left sidebar
2. Database name: `rental_management`
3. Collation: `utf8mb4_unicode_ci`
4. Click "Create"

### Step 5: Update Configuration

Edit `C:\xampp\htdocs\rental-management\config\app_local.php` file:

```php
'Datasources' => [
    'default' => [
        'host' => 'localhost',
        'username' => 'root',      // XAMPP default
        'password' => '',          // XAMPP default (empty)
        'database' => 'rental_management',
    ],
],
```

### Step 6: Run Migrations

In PowerShell:

```powershell
cd C:\xampp\htdocs\rental-management
C:\xampp\php\php.exe bin\cake.php migrations migrate
```

### Step 7: Seed Database (Optional)

To add default users:

```powershell
C:\xampp\php\php.exe bin\cake.php migrations seed --seed UsersSeeder
```

### Step 8: Access Application

Open in browser:
- **Application**: http://localhost/rental-management
- **phpMyAdmin**: http://localhost/phpmyadmin

## Default Login (After Seeding)

- **Admin**: admin@rental.com / admin123
- **Landlord**: landlord@rental.com / landlord123
- **Tenant**: tenant@rental.com / tenant123

## Troubleshooting

### Composer Not Found
```powershell
# Install Composer globally
# Or download composer.phar to project folder
php composer.phar install
```

### Database Connection Error
- Check if MySQL is running
- Verify credentials in `config/app_local.php`
- Check if database was created

### 404 Not Found
- Check if `.htaccess` files exist
- Enable Apache mod_rewrite:
  - Edit `C:\xampp\apache\conf\httpd.conf` file
  - Remove `#` from `#LoadModule rewrite_module` line
  - Restart Apache

## Useful Commands

```powershell
# Migrations
C:\xampp\php\php.exe bin\cake.php migrations migrate

# Seed database
C:\xampp\php\php.exe bin\cake.php migrations seed --seed UsersSeeder

# Check PHP version
C:\xampp\php\php.exe -v

# Composer install
composer install
# Or
php composer.phar install
```

## Next Steps

1. ✅ Local setup complete
2. ⏭️ Configure Firebase
3. ⏭️ Test application
4. ⏭️ Production deployment (Vercel/cPanel)
