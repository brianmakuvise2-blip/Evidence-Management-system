<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralNotification;
use App\Notifications\AccountLockoutNotification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping notification seeding.');
            return;
        }

        // Create various types of notifications
        $notificationTypes = [
            'account_lockout' => 'Account locked due to multiple failed login attempts',
            'password_expiry' => 'Your password will expire in 7 days',
            'evidence_transfer' => 'Evidence transfer request requires your approval',
            'bundle_approved' => 'Court bundle has been approved',
            'system_maintenance' => 'System maintenance scheduled for tonight',
            'security_alert' => 'Unusual login activity detected',
        ];

        $notificationCount = 0;

        // Create notifications for random users
        for ($i = 0; $i < 25; $i++) {
            $user = $users->random();
            $type = array_rand($notificationTypes);

            // Create database notification using Laravel's built-in method
            $user->notify(new GeneralNotification([
                'title' => ucfirst(str_replace('_', ' ', $type)),
                'message' => $notificationTypes[$type],
                'type' => $type,
                'action_url' => $this->getActionUrl($type),
                'action_text' => $this->getActionText($type),
            ]));
        }

        // Create some account lockout notifications
        $adminUsers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['administrator', 'system-administrator']);
        })->get();

        if ($adminUsers->isNotEmpty()) {
            for ($i = 0; $i < 5; $i++) {
                $lockedUser = $users->random();
                $admin = $adminUsers->random();

                // Send account lockout notification
                $admin->notify(new AccountLockoutNotification($lockedUser, now()->addMinutes(30)));

                $notificationCount++;
            }
        }

        $this->command->info('✓ Notifications seeded: ' . $notificationCount . ' notifications created');
    }

    /**
     * Get action URL for notification type
     */
    private function getActionUrl(string $type): ?string
    {
        return match($type) {
            'account_lockout' => '/admin/users',
            'password_expiry' => '/profile/change-password',
            'evidence_transfer' => '/transfers',
            'bundle_approved' => '/bundles',
            'system_maintenance' => '/dashboard',
            'security_alert' => '/profile',
            default => null,
        };
    }

    /**
     * Get action text for notification type
     */
    private function getActionText(string $type): ?string
    {
        return match($type) {
            'account_lockout' => 'View Users',
            'password_expiry' => 'Change Password',
            'evidence_transfer' => 'View Transfers',
            'bundle_approved' => 'View Bundle',
            'system_maintenance' => 'View Dashboard',
            'security_alert' => 'Review Profile',
            default => null,
        };
    }
}