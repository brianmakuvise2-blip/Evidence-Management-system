<?php

namespace Database\Seeders;

use App\Models\UserActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping user activity log seeding.');
            return;
        }

        $activities = [
            'login' => 'User logged into the system',
            'logout' => 'User logged out of the system',
            'view_evidence' => 'Viewed evidence item',
            'create_evidence' => 'Created new evidence item',
            'edit_evidence' => 'Modified evidence item',
            'delete_evidence' => 'Deleted evidence item',
            'transfer_request' => 'Requested evidence transfer',
            'approve_transfer' => 'Approved transfer request',
            'reject_transfer' => 'Rejected transfer request',
            'acknowledge_receipt' => 'Acknowledged evidence receipt',
            'view_bundle' => 'Viewed court bundle',
            'create_bundle' => 'Created court bundle',
            'approve_bundle' => 'Approved court bundle',
            'view_reports' => 'Accessed reports',
            'user_management' => 'Performed user management action',
            'profile_update' => 'Updated user profile',
            'password_change' => 'Changed password',
            'failed_login' => 'Failed login attempt',
            'account_lockout' => 'Account locked due to failed attempts',
        ];

        $ipAddresses = [
            '192.168.1.100', '192.168.1.101', '192.168.1.102',
            '10.0.0.50', '10.0.0.51', '10.0.0.52',
            '172.16.0.10', '172.16.0.11', '172.16.0.12',
        ];

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ];

        // Create activity logs for each user
        foreach ($users as $user) {
            $activityCount = rand(5, 20);

            for ($i = 0; $i < $activityCount; $i++) {
                $activityType = array_rand($activities);
                $activityAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

                UserActivityLog::create([
                    'user_id' => $user->id,
                    'action' => $activityType,
                    'ip_address' => $ipAddresses[array_rand($ipAddresses)],
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'details' => [
                        'description' => $activities[$activityType],
                        'url' => '/' . ['dashboard', 'evidence', 'transfers', 'bundles', 'reports', 'profile'][rand(0, 5)],
                        'method' => ['GET', 'POST', 'PUT', 'DELETE'][rand(0, 3)],
                        'response_code' => rand(0, 10) > 8 ? 403 : 200,
                        'timestamp' => $activityAt,
                    ],
                    'status' => rand(0, 10) > 8 ? 'failure' : 'success',
                ]);
            }
        }

        $this->command->info('✓ User activity logs seeded: ' . UserActivityLog::count() . ' activity records created');
    }
}