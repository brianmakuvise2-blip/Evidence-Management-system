<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class SettingsService
{
    protected string $settingsFile = 'settings.json';

    protected array $defaults = [
        'app_name' => 'EvidenceEMS — Zimbabwe',
        'app_email' => 'admin@evidence.gov.zw',
        'items_per_page' => 50,
        'session_timeout' => 120,
        'enable_mfa' => false,
        'password_expiry_days' => 90,
        'max_login_attempts' => 5,
        'lockout_duration_minutes' => 15,
        'evidence_instructions' => "When evidence is uploaded by any organisation, the following steps must be taken:\n1. Verify the evidence is within your jurisdiction and case scope.\n2. Coordinate with the uploading institution to confirm chain-of-custody integrity.\n3. Follow standard evidence handling and storage protocols.\n4. Record receipt in the chain-of-custody log within 24 hours.\n5. Escalate any integrity concerns to your system administrator immediately.",
        'cross_institution_notify' => true,
    ];

    public function all(): array
    {
        if (Storage::exists($this->settingsFile)) {
            $data = json_decode(Storage::get($this->settingsFile), true);
            return array_merge($this->defaults, $data ?? []);
        }
        return $this->defaults;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default ?? $this->defaults[$key] ?? null;
    }

    public function set(array $values): void
    {
        $current = $this->all();
        $merged = array_merge($current, $values);
        Storage::put($this->settingsFile, json_encode($merged, JSON_PRETTY_PRINT));
    }
}
