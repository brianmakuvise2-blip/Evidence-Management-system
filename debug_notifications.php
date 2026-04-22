<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

// Check admin count
$adminCount = User::whereHas('roles', function($q) {
    $q->whereIn('name', ['administrator', 'system-administrator']);
})->count();

echo "Administrator count: $adminCount\n";

// Check total notifications
$totalNotifications = DB::table('notifications')->count();
echo "Total notifications: $totalNotifications\n";

// Check recent notifications
$recentNotifications = DB::table('notifications')
    ->orderBy('created_at', 'desc')
    ->take(3)
    ->get();

echo "Recent notifications:\n";
foreach ($recentNotifications as $notification) {
    echo "- ID: {$notification->id}, Type: {$notification->type}, Created: {$notification->created_at}\n";
    $data = json_decode($notification->data, true);
    echo "  Title: " . ($data['title'] ?? 'N/A') . "\n";
    echo "  Message: " . ($data['message'] ?? 'N/A') . "\n";
}