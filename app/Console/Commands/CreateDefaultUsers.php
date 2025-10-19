<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateDefaultUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create-defaults';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default admin, agent and user accounts for testing';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Creating default users...');

        // Create admin if doesn't exist
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            User::create([
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'phone' => '123-456-7890',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'status' => 'active',
            ]);
            $this->info('✅ Admin user created: admin@gmail.com / admin');
        } else {
            $this->info('ℹ️  Admin user already exists: ' . $admin->email);
        }

        // Update agent password to 111
        $agent = User::where('role', 'agent')->first();
        if ($agent) {
            $agent->update(['password' => Hash::make('111')]);
            $this->info('✅ Agent password updated: ' . $agent->username . ' / 111');
        } else {
            User::create([
                'name' => 'Agent',
                'username' => 'agent',
                'email' => 'agent@gmail.com',
                'phone' => '987-654-3210',
                'password' => Hash::make('111'),
                'role' => 'agent',
                'status' => 'active',
            ]);
            $this->info('✅ Agent user created: agent / 111');
        }

        // Create user if doesn't exist
        $user = User::where('role', 'user')->first();
        if (!$user) {
            User::create([
                'name' => 'User',
                'username' => 'user',
                'email' => 'user@gmail.com',
                'phone' => '555-123-4567',
                'password' => Hash::make('user'),
                'role' => 'user',
                'status' => 'active',
            ]);
            $this->info('✅ User account created: user@gmail.com / user');
        } else {
            $this->info('ℹ️  User account already exists: ' . $user->email);
        }

        $this->info('');
        $this->info('🎉 All default users are ready!');
        $this->info('Login credentials:');
        $this->info('• Admin: admin@gmail.com / admin');
        $this->info('• Agent: agent / 111');
        $this->info('• User: user@gmail.com / user');
    }
}
