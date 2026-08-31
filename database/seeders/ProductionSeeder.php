<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Seeds only what a real deployment needs: roles/permissions and one
     * Super Admin account, driven by .env — no demo school, classes,
     * students, or sample records. Use this instead of DatabaseSeeder
     * (which also runs DemoDataSeeder) when going live.
     *
     * Configure SUPER_ADMIN_NAME / SUPER_ADMIN_EMAIL / SUPER_ADMIN_PASSWORD
     * in .env before running, then: php artisan db:seed --class=ProductionSeeder
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@example.com');
        $password = env('SUPER_ADMIN_PASSWORD');

        if (! $password) {
            $this->command?->warn(
                'SUPER_ADMIN_PASSWORD not set in .env — skipping Super Admin creation. '.
                'Set it and re-run: php artisan db:seed --class=ProductionSeeder'
            );

            return;
        }

        $superAdmin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
                'password' => $password,
                'status' => 'active',
            ]
        );

        if (! $superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }

        $this->command?->info("Super Admin ready: {$email}");
    }
}
