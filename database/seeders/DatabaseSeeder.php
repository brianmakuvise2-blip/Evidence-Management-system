<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First run role and permission seeder
        $this->call(RolePermissionSeeder::class);
        
        // Then seed test data with institutions and users
        $this->call(TestDataSeeder::class);

        // Seed additional evidence
        $this->call(EvidenceSeeder::class);

        // Seed chain of custody records
        $this->call(ChainOfCustodySeeder::class);

        // Seed transfer requests
        $this->call(TransferRequestSeeder::class);

        // Seed court bundles and related data
        $this->call(CourtBundleSeeder::class);

        // Seed user activity logs
        $this->call(UserActivityLogSeeder::class);

        // Seed notifications
        $this->call(NotificationSeeder::class);
    }
}
