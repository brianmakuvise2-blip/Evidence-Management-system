# Institution-Specific Role-Based Access Control

## Overview
This document outlines the new institution-specific role-based access control system implemented for the Evidence Management System.

## Institution Roles and Permissions

### 1. Super Admin (City of Bulawayo - COB)
**Role:** `super-admin`
**Institution:** City of Bulawayo (Originating Institute)
**Permissions:**
- Full system access including audit logs and settings management
- Can create and manage other institution admins
- All evidence, user, and system management capabilities

### 2. RBZ System Admin
**Role:** `rbz-system-admin`
**Institution:** Reserve Bank of Zimbabwe
**Permissions:**
- Limited evidence registration within RBZ
- User management within institution
- View audit logs
- Manage notifications

### 3. ZACC System Admin
**Role:** `zacc-system-admin`
**Institution:** Zimbabwe Anti-Corruption Commission
**Permissions:**
- Limited evidence registration within ZACC
- User management within institution
- View audit logs
- Manage notifications

### 4. NPA System Admin
**Role:** `npa-system-admin`
**Institution:** National Prosecuting Authority
**Permissions:**
- Evidence registration and management
- **Retrieve evidence for prosecution and trial preparation**
- Prepare and view court bundles
- Disclose evidence
- View audit logs
- Manage notifications

### 5. ZRP System Admin
**Role:** `zrp-system-admin`
**Institution:** Zimbabwe Republic Police
**Permissions:**
- Evidence registration and management
- **Register seizure documents and exhibits**
- User management within institution
- View audit logs
- Manage notifications

### 6. Judicial System Admin
**Role:** `judicial-system-admin`
**Institution:** Judiciary
**Permissions:**
- **Access approved evidence bundles and orders**
- User management within institution
- View audit logs
- Manage notifications

### 7. Judicial Courts Admin
**Role:** `judicial-courts-admin`
**Institution:** Judiciary (Courts)
**Permissions:**
- **Archive, retention, and disposal of evidence**
- User management within institution
- View evidence bundles
- View audit logs
- Manage notifications

## Module Access Matrix

| Module | Super Admin | RBZ Admin | ZACC Admin | NPA Admin | ZRP Admin | Judicial Admin | Courts Admin |
|--------|-------------|-----------|------------|-----------|-----------|----------------|--------------|
| Audit Logs & Settings | ✅ Full Access | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Audit Logs Only | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Notifications | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Evidence Registration | ✅ | ✅ Limited | ✅ Limited | ✅ | ✅ | ❌ | ❌ |
| Evidence Retrieval | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ |
| Seizure Registration | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| Bundle Access | ✅ | ❌ | ❌ | ✅ | ❌ | ✅ | ✅ |
| Archive/Disposal | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |

## Implementation Notes

1. **Institution-Based Restrictions**: All institution admins are restricted to their own institution's data
2. **Super Admin Override**: COB super admin has cross-institution access
3. **Audit Trail**: All actions are logged for compliance
4. **Notification System**: All admins receive notifications regardless of institution

## Database Seeding

Run the following command to apply these roles and permissions:

```bash
php artisan db:seed --class=RolePermissionSeeder
```

## User Assignment

Assign roles to users based on their institution and responsibilities:

```php
// Example: Assign COB super admin
$user = User::where('institution_id', 1)->first(); // COB institution
$user->assignRole('super-admin');

// Example: Assign RBZ system admin
$user = User::where('institution_id', 3)->first(); // RBZ institution
$user->assignRole('rbz-system-admin');
```