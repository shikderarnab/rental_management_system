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

6. **Access**: http://localhost/rental-management

## Usage

### Default Credentials (after seeding)

- **Admin**: admin@rental.com / admin123
- **Landlord**: landlord@rental.com / landlord123
- **Tenant**: tenant@rental.com / tenant123

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

## Support

For issues and questions, please open an issue on the repository.

