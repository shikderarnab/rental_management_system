<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

class UsersSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'email' => 'admin@rental.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'is_active' => true,
                'email_verified' => true,
                'phone_verified' => false,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ],
            [
                'email' => 'landlord@rental.com',
                'password' => password_hash('landlord123', PASSWORD_DEFAULT),
                'role' => 'landlord',
                'first_name' => 'John',
                'last_name' => 'Landlord',
                'phone' => '+1234567890',
                'is_active' => true,
                'email_verified' => true,
                'phone_verified' => false,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ],
            [
                'email' => 'tenant@rental.com',
                'password' => password_hash('tenant123', PASSWORD_DEFAULT),
                'role' => 'tenant',
                'first_name' => 'Jane',
                'last_name' => 'Tenant',
                'phone' => '+1234567891',
                'is_active' => true,
                'email_verified' => true,
                'phone_verified' => false,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ],
        ];

        $table = $this->table('users');
        $table->insert($data)->save();
    }
}

