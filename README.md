# Rental Management System

A complete Rental Management System built with CakePHP 4.x, MySQL, and Firebase integration for SMS and Email notifications.

## Features

- **User Roles**: Admin, Landlord, Tenant
- **Property & Unit Management**: Landlords can manage properties and units
- **Rental Agreements**: Upload PDF agreements with digital signature support
- **Manual Payment System**: Cash and Bank transfer payments with verification workflow
- **Firebase Integration**: Free SMS (Phone Auth) and Email notifications
- **Payment Verification**: Landlords can verify/reject payments with proof
- **PDF Invoices/Receipts**: Automatic PDF generation for verified payments
- **Dispute System**: Tenants can submit disputes, landlords/admins can respond
- **Reminder System**: Automated reminders for rent due dates
- **Responsive UI**: Bootstrap 5 based responsive design

## Requirements

- PHP 8.0+
- MySQL 8.0+
- Composer
- Firebase Account (for SMS/Email)
- Apache/Nginx with mod_rewrite

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd rental-management
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment

Copy `config/app_local.php.example` to `config/app_local.php` and update:

```php
'Datasources' => [
    'default' => [
        'host' => 'localhost',
        'username' => 'your_db_user',
        'password' => 'your_db_password',
        'database' => 'rental_management',
    ],
],
'Firebase' => [
    'apiKey' => 'your-firebase-api-key',
    'authDomain' => 'your-project.firebaseapp.com',
    'projectId' => 'your-project-id',
    // ... other Firebase config
],
```

### 4. Set up Firebase

1. Create a Firebase project at https://console.firebase.google.com
2. Enable Phone Authentication
3. Download service account JSON file and place it in `config/firebase-service-account.json`
4. Update Firebase configuration in `config/app_local.php`

### 5. Run migrations

```bash
bin/cake migrations migrate
```

### 6. Seed database (optional)

```bash
bin/cake migrations seed --seed UsersSeeder
```

### 7. Set permissions

```bash
chmod -R 755 tmp logs webroot/uploads
```

## XAMPP Setup (Recommended for Windows)

### Quick Setup

1. **Install XAMPP**: https://www.apachefriends.org/
2. **Run setup script**:
   ```powershell
   .\setup-xampp.ps1
   ```
3. **Start Apache & MySQL** in XAMPP Control Panel
4. **Create database** in phpMyAdmin: `rental_management`
5. **Run migrations**:
   ```powershell
   cd C:\xampp\htdocs\rental-management
   C:\xampp\php\php.exe bin\cake.php migrations migrate
   ```
6. **Access**: http://localhost/rental-management

See **[XAMPP_SETUP.md](XAMPP_SETUP.md)** for detailed instructions.

## Usage

### Default Credentials (after seeding)

- **Admin**: admin@rental.com / admin123
- **Landlord**: landlord@rental.com / landlord123
- **Tenant**: tenant@rental.com / tenant123

### Setting up Cron Job for Reminders

#### Windows (Task Scheduler)
1. Open Task Scheduler
2. Create Basic Task
3. Trigger: Daily at 9 AM
4. Action: Start a program
5. Program: `C:\xampp\php\php.exe`
6. Arguments: `C:\xampp\htdocs\rental-management\bin\cake.php reminder`

#### Linux/Mac (Crontab)
```bash
0 9 * * * cd /path/to/rental-management && bin/cake reminder
```

## API Endpoints

### Firebase Phone Auth

- `POST /api/firebase/send-otp` - Send OTP SMS
  ```json
  {
    "phone_number": "+1234567890"
  }
  ```

- `POST /api/firebase/verify-otp` - Verify OTP
  ```json
  {
    "session_info": "session_info_from_send_otp",
    "code": "123456"
  }
  ```

## Project Structure

```
rental-management/
├── config/              # Configuration files
│   ├── Migrations/      # Database migrations
│   └── Seeds/          # Database seeders
├── src/
│   ├── Controller/     # Controllers
│   ├── Model/          # Models and Entities
│   ├── Service/        # Service classes (Firebase, PDF)
│   └── Command/        # Console commands
├── templates/          # View templates
├── webroot/            # Public files
│   └── uploads/        # Uploaded files
└── tests/              # Unit tests
```

## Firebase Integration

### SMS Notifications

The system uses Firebase Phone Authentication API to send OTP SMS for:
- Payment verification notifications
- Payment rejection notifications
- Rent due reminders

### Email Notifications

Email notifications are sent via Firebase Cloud Functions (requires deployment):
- Payment verified
- Payment rejected
- Agreement signed
- Rent due reminders

### Setting up Firebase Cloud Functions

1. Install Firebase CLI: `npm install -g firebase-tools`
2. Initialize Firebase: `firebase init functions`
3. Deploy the email function (see `firebase/functions/index.js`)

## Payment Methods

- **Cash**: Submit payment with optional receipt photo
- **Bank Transfer**: Upload transfer slip with reference number
- **Online Payment**: Disabled (shows "Coming Soon")

## Security

- Password hashing using PHP `password_hash()`
- CSRF protection enabled
- File upload validation
- Role-based access control
- SQL injection prevention (CakePHP ORM)

## Testing

```bash
bin/cake test
```

## Troubleshooting

### Firebase SMS not working

1. Verify Firebase API key is correct
2. Check Phone Authentication is enabled in Firebase Console
3. Ensure phone numbers are in E.164 format (+1234567890)

### Email not sending

1. Verify Firebase Cloud Functions are deployed
2. Check service account JSON file path
3. Review logs in `logs/error.log`

### File upload issues

1. Check `webroot/uploads` directory permissions
2. Verify PHP upload_max_filesize and post_max_size settings

## License

MIT License

## Architecture Documentation

For detailed architecture information, see:
- **[ARCHITECTURE.md](ARCHITECTURE.md)** - Complete system architecture documentation
- **[ARCHITECTURE_DIAGRAMS.md](ARCHITECTURE_DIAGRAMS.md)** - Visual architecture diagrams

## Support

For issues and questions, please open an issue on the repository.

