# EvidenceEMS — Zimbabwe Evidence Management System

## Overview
Laravel 12.x Evidence Management System for Zimbabwe. Manages evidence chain-of-custody with role-based access control across multiple institutions (ZRP, NPA, RBZ, ZACC, Judiciary, City of Bulawayo).

## Tech Stack
- **Backend**: Laravel 12.x / PHP 8.2
- **Database**: SQLite (at `database/database.sqlite`)
- **Auth**: Spatie Permission (roles + permissions)
- **Session/Cache**: File-based
- **Server**: `php artisan serve` on port 5000

## Starting the App
```
bash start.sh
```
Workflow: `Start application`

## Login Credentials
- **Super Admin**: `superadmin@cob.gov.zw` / `password123`
- Role: `super-admin`, Institution: City of Bulawayo (id=1)

## Key Config
- `start.sh` — exports env vars + runs artisan serve
- `config/database.php` — hardcoded SQLite default
- `config/session.php` — driver=file, samesite=none, secure=true
- `config/cache.php` — default=file
- `bootstrap/app.php` — trustProxies(at:'*')
- `app/Providers/AppServiceProvider.php` — URL::forceScheme('https')
- `app/Services/SettingsService.php` — settings persistence to `storage/app/settings.json`

## Architecture

### Models
- `Evidence` — core entity with SHA-256 file hash, status lifecycle
- `EvidenceHashHistory` — integrity hash log per evidence change
- `ChainOfCustody` — custody transfer records
- `TransferRequest` — multi-step transfer workflow
- `Institution` / `Department` — org structure
- `User` — with MFA, password policy, Spatie roles
- `CourtBundle` / `CourtBundleItem` — court disclosure packages

### Key Controllers
- `EvidenceController` — CRUD, file hash, integrity tracking, cross-institution notifications
- `Admin\UserManagementController` — user CRUD per institution
- `Admin\RoleManagementController` — role & permission management per institution
- `Admin\SettingsController` — system settings with JSON persistence + cache clear
- `TransferRequestController` — transfer workflow (request → approve → acknowledge)
- `NotificationController` — DB notifications for all events

## Features Implemented

### Chain of Custody
- Full custody chain on evidence show page with Full History link
- Transfer requests with approval workflow
- Digital signatures on acknowledgment

### Evidence Integrity (Hash Tracking)
- SHA-256 hash generated on every upload
- New hash recorded on ANY change (title, description, type, file, etc.)
- Old vs new hash stored in `evidence_hash_history` table
- Hash history displayed on evidence show page with change details
- Admins notified with old hash vs new hash on every change
- Tampering detection (hash mismatch flagged in red)

### Role Management Per Institution
- Route: `/admin/roles` — list all roles grouped by institution
- Create custom roles with permission assignment
- Institution prefix auto-applied to role names
- Protected core roles (super-admin, administrator, etc.)

### Settings (Admin)
- Route: `/settings` — persistent settings via `storage/app/settings.json`
- General settings: app name, email, pagination, session timeout
- Security settings: password expiry, login attempts, lockout, MFA toggle
- Cross-institution notifications: on/off toggle + customizable instructions text
- Working "Clear Cache" button via AJAX → POST /settings/clear-cache

### Cross-Institution Evidence Instructions
- When evidence uploaded, all institution admins notified
- Instructions text (configurable in Settings) included in notification for other-org admins
- Personalised: same-institution admins get standard notification, other orgs get instructions
- Toggle in Settings to enable/disable cross-institution notifications

### Notifications
- DB notifications for: evidence created, updated, verified, archived, deleted
- Hash change notifications include old/new hash values
- All system/institution admin roles receive notifications
- Bell icon in sidebar with unread count

## Routes Summary
- `/settings` — System Settings (super-admin only)
- `/settings/clear-cache` — POST, AJAX cache clear
- `/admin/roles` — Role Management (super-admin only)
- `/admin/roles/create` — Create Role
- `/admin/roles/{role}/edit` — Edit Role Permissions
- `/admin/users` — User Management
- `/evidence` — Evidence list
- `/evidence/{id}` — Evidence detail with hash history
- `/transfers` — Transfer requests
- `/audit-logs` — Audit logs
- `/notifications` — Notifications inbox

## Institutions (Seeded)
1. City of Bulawayo (COB) — super-admin
2. Zimbabwe Republic Police (ZRP)
3. Reserve Bank of Zimbabwe (RBZ)
4. National Prosecuting Authority (NPA)
5. Zimbabwe Anti-Corruption Commission (ZACC)
6. Judiciary

## Roles (Seeded)
`super-admin`, `rbz-system-admin`, `zacc-system-admin`, `npa-system-admin`, `zrp-system-admin`, `judicial-system-admin`, `judicial-courts-admin`, `system-administrator`, `administrator`, `source-officer`, `investigator`, `financial-verifier`, `prosecutor`, `judicial-viewer`, `supervisor`, `evidence-officer`, `user`
