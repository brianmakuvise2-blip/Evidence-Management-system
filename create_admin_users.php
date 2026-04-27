<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Institution;
use Illuminate\Support\Facades\Hash;

echo "🔐 Creating admin users for each institution...\n\n";

$testUsers = [
    [
        'name' => 'Super Admin',
        'email' => 'superadmin@evidence.gov.zw',
        'password' => 'SuperAdmin123!',
        'institution_name' => 'City of Bulawayo',
        'role' => 'super-admin',
        'job_title' => 'Super Administrator',
        'employee_id' => 'SA001'
    ],
    [
        'name' => 'RBZ System Admin',
        'email' => 'rbzadmin@rbz.gov.zw',
        'password' => 'RBZAdmin123!',
        'institution_name' => 'Reserve Bank of Zimbabwe',
        'role' => 'rbz-system-admin',
        'job_title' => 'System Administrator',
        'employee_id' => 'RBZ001'
    ],
    [
        'name' => 'ZACC System Admin',
        'email' => 'zaccadmin@zacc.gov.zw',
        'password' => 'ZACCAdmin123!',
        'institution_name' => 'Zimbabwe Anti-Corruption Commission',
        'role' => 'zacc-system-admin',
        'job_title' => 'System Administrator',
        'employee_id' => 'ZACC001'
    ],
    [
        'name' => 'NPA System Admin',
        'email' => 'npaadmin@npa.gov.zw',
        'password' => 'NPAAdmin123!',
        'institution_name' => 'National Prosecuting Authority',
        'role' => 'npa-system-admin',
        'job_title' => 'System Administrator',
        'employee_id' => 'NPA001'
    ],
    [
        'name' => 'ZRP System Admin',
        'email' => 'zrpadimin@zrp.gov.zw',
        'password' => 'ZRPAdmin123!',
        'institution_name' => 'Zimbabwe Republic Police',
        'role' => 'zrp-system-admin',
        'job_title' => 'System Administrator',
        'employee_id' => 'ZRP001'
    ],
    [
        'name' => 'Judicial System Admin',
        'email' => 'judicialadmin@judicial.gov.zw',
        'password' => 'JudicialAdmin123!',
        'institution_name' => 'Judiciary',
        'role' => 'judicial-system-admin',
        'job_title' => 'System Administrator',
        'employee_id' => 'JUD001'
    ],
    [
        'name' => 'Courts System Admin',
        'email' => 'courtsadmin@courts.gov.zw',
        'password' => 'CourtsAdmin123!',
        'institution_name' => 'Judiciary',
        'role' => 'judicial-courts-admin',
        'job_title' => 'System Administrator',
        'employee_id' => 'CRT001'
    ]
];

$createdUsers = [];

foreach ($testUsers as $userData) {
    $institution = Institution::where('name', $userData['institution_name'])->first();

    if ($institution) {
        // Check if user already exists
        $existingUser = User::where('email', $userData['email'])->first();

        if ($existingUser) {
            echo "ℹ️  User already exists: {$userData['name']} - {$userData['email']}\n";
            $user = $existingUser;
        } else {
            // Generate unique employee ID
            $baseId = $userData['employee_id'];
            $employeeId = $baseId;
            $counter = 1;
            while (User::where('employee_id', $employeeId)->exists()) {
                $employeeId = $baseId . '-' . $counter;
                $counter++;
            }

            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'institution_id' => $institution->id,
                'department_id' => $institution->departments()->first()->id ?? null,
                'job_title' => $userData['job_title'],
                'employee_id' => $employeeId,
                'is_active' => true
            ]);
            echo "✅ Created: {$userData['name']} - {$userData['email']} - Role: {$userData['role']}\n";
        }

        // Ensure role is assigned
        if (!$user->hasRole($userData['role'])) {
            $user->assignRole($userData['role']);
            echo "🎭 Assigned role: {$userData['role']} to {$userData['name']}\n";
        }

        $createdUsers[] = [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'],
            'role' => $userData['role'],
            'institution' => $institution->name
        ];
    } else {
        echo "❌ Institution '{$userData['institution_name']}' not found\n";
    }
}

echo "\n🎯 ADMIN LOGIN CREDENTIALS:\n";
echo "==========================\n\n";

foreach ($createdUsers as $user) {
    echo "👤 {$user['name']}\n";
    echo "🏢 Institution: {$user['institution']}\n";
    echo "📧 Email: {$user['email']}\n";
    echo "🔑 Password: {$user['password']}\n";
    echo "🎭 Role: {$user['role']}\n";
    echo "─" . str_repeat("─", 50) . "\n\n";
}

echo "🚀 LOGIN URL: http://localhost/evidence-management-system/public/login\n\n";

echo "📋 QUICK REFERENCE:\n";
echo "===================\n";
echo "• Super Admin: superadmin@evidence.gov.zw / SuperAdmin123!\n";
echo "• RBZ Admin: rbzadmin@rbz.gov.zw / RBZAdmin123!\n";
echo "• ZACC Admin: zaccadmin@zacc.gov.zw / ZACCAdmin123!\n";
echo "• NPA Admin: npaadmin@npa.gov.zw / NPAAdmin123!\n";
echo "• ZRP Admin: zrpadimin@zrp.gov.zw / ZRPAdmin123!\n";
echo "• Judicial Admin: judicialadmin@judicial.gov.zw / JudicialAdmin123!\n";
echo "• Courts Admin: courtsadmin@courts.gov.zw / CourtsAdmin123!\n\n";

echo "✅ All admin users created and ready for login!\n";