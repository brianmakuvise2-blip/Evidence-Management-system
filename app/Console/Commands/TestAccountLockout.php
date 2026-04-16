<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestAccountLockout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:account-lockout {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestAccountLockout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:account-lockout {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test account lockout notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'supervisor@zrp.local';

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found");
            return 1;
        }

        $this->info("Testing account lockout for user: {$user->name} ({$user->email})");

        // Reset failed attempts first
        $user->update(['failed_login_attempts' => 0, 'lockout_expires_at' => null]);
        $this->info("Reset failed attempts to 0");

        // Trigger 5 failed login attempts
        for ($i = 1; $i <= 5; $i++) {
            $this->info("Triggering failed login attempt {$i}/5...");
            $user->recordFailedLoginAttempt();
        }

        $this->info("Account should now be locked. Checking administrators...");

        // Check if administrators received notifications
        $admins = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['administrator', 'system-administrator']);
        })->get();

        foreach ($admins as $admin) {
            $lockoutNotifications = $admin->notifications()
                ->where('type', 'App\Notifications\AccountLockoutNotification')
                ->where('created_at', '>=', now()->subMinutes(5))
                ->count();

            if ($lockoutNotifications > 0) {
                $this->info("✓ Administrator {$admin->name} received {$lockoutNotifications} lockout notification(s)");
            } else {
                $this->warn("✗ Administrator {$admin->name} did not receive lockout notifications");
            }
        }

        $this->info("Test completed. Check the notification badge and dropdown in the UI.");

        return 0;
    }
}
}
