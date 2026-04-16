<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class AccountLockoutNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $lockedUser;
    public $lockoutExpiresAt;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $lockedUser, $lockoutExpiresAt)
    {
        $this->lockedUser = $lockedUser;
        $this->lockoutExpiresAt = $lockoutExpiresAt;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Account Lockout Alert - Security Incident')
            ->greeting('Security Alert')
            ->line('An account has been temporarily locked due to multiple failed login attempts.')
            ->line('**Account Details:**')
            ->line('User: ' . $this->lockedUser->name . ' (' . $this->lockedUser->email . ')')
            ->line('Employee ID: ' . ($this->lockedUser->employee_id ?? 'N/A'))
            ->line('Institution: ' . ($this->lockedUser->institution->name ?? 'N/A'))
            ->line('Department: ' . ($this->lockedUser->department->name ?? 'N/A'))
            ->line('Lockout expires: ' . $this->lockoutExpiresAt->format('M d, Y H:i:s'))
            ->line('This may indicate a security threat. Please investigate and consider resetting the user\'s password if appropriate.')
            ->action('View User Management', url('/admin/users/' . $this->lockedUser->id))
            ->salutation('Regards, Evidence Management System Security');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Account Lockout Alert',
            'message' => 'User ' . $this->lockedUser->name . ' has been locked out due to failed login attempts.',
            'user_id' => $this->lockedUser->id,
            'user_name' => $this->lockedUser->name,
            'user_email' => $this->lockedUser->email,
            'lockout_expires_at' => $this->lockoutExpiresAt->toDateTimeString(),
            'type' => 'account_lockout',
            'action_url' => '/admin/users/' . $this->lockedUser->id,
        ];
    }
}