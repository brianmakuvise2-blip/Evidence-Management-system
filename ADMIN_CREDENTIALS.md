🔐 **EVIDENCE MANAGEMENT SYSTEM - ADMIN LOGIN CREDENTIALS**
================================================================

Here are the login credentials for all institution admin roles:

## 🎯 **ADMIN LOGIN CREDENTIALS**

### 1️⃣ **SUPER ADMIN (City of Bulawayo)**
- **👤 Name:** City of Bulawayo Super Admin
- **📧 Email:** superadmin@cob.gov.zw
- **🔑 Password:** password123
- **🏢 Institution:** City of Bulawayo
- **🎭 Role:** super-admin
- **✅ Access:** Full system control, audit logs & settings, manage all institutions

### 2️⃣ **RBZ SYSTEM ADMIN**
- **👤 Name:** RBZ System Admin
- **📧 Email:** admin@rbz.org.zw
- **🔑 Password:** password123
- **🏢 Institution:** Reserve Bank of Zimbabwe
- **🎭 Role:** rbz-system-admin
- **✅ Access:** Evidence registration (RBZ only), user management, audit logs

### 3️⃣ **ZACC SYSTEM ADMIN**
- **👤 Name:** ZACC System Admin
- **📧 Email:** admin@zacc.gov.zw
- **🔑 Password:** password123
- **🏢 Institution:** Zimbabwe Anti-Corruption Commission
- **🎭 Role:** zacc-system-admin
- **✅ Access:** Evidence registration (ZACC only), user management, audit logs

### 4️⃣ **NPA SYSTEM ADMIN**
- **👤 Name:** NPA System Admin
- **📧 Email:** admin@npa.gov.zw
- **🔑 Password:** password123
- **🏢 Institution:** National Prosecuting Authority
- **🎭 Role:** npa-system-admin
- **✅ Access:** Evidence registration, retrieve evidence for prosecution, prepare bundles, disclose evidence

### 5️⃣ **ZRP SYSTEM ADMIN**
- **👤 Name:** ZRP System Admin
- **📧 Email:** admin@zrp.gov.zw
- **🔑 Password:** password123
- **🏢 Institution:** Zimbabwe Republic Police
- **🎭 Role:** zrp-system-admin
- **✅ Access:** Evidence registration, register seizure documents/exhibits, custody management

### 6️⃣ **JUDICIAL SYSTEM ADMIN**
- **👤 Name:** Judicial System Admin
- **📧 Email:** admin@judiciary.gov.zw
- **🔑 Password:** password123
- **🏢 Institution:** Judiciary
- **🎭 Role:** judicial-system-admin
- **✅ Access:** Access approved evidence bundles and orders, audit logs

### 7️⃣ **JUDICIAL COURTS ADMIN**
- **👤 Name:** Judicial Courts Admin
- **📧 Email:** courts@judiciary.gov.zw
- **🔑 Password:** password123
- **🏢 Institution:** Judiciary (Courts)
- **🎭 Role:** judicial-courts-admin
- **✅ Access:** Archive evidence, manage retention/disposal, view bundles

## 🚀 **HOW TO USE THESE CREDENTIALS**

1. **Start your XAMPP server** (Apache + MySQL)
2. **Run the database seeder:**
   ```bash
   php artisan db:seed --class=RolePermissionSeeder
   ```
3. **Create the admin users:**
   ```bash
   php create_admin_users.php
   ```
4. **Go to your application login page**
5. **Use the email/password combinations above**

## 📋 **QUICK LOGIN REFERENCE**

| Institution | Email | Password | Role |
|-------------|-------|----------|------|
| COB Super Admin | superadmin@cob.gov.zw | password123 | super-admin |
| RBZ Admin | admin@rbz.org.zw | password123 | rbz-system-admin |
| ZACC Admin | admin@zacc.gov.zw | password123 | zacc-system-admin |
| NPA Admin | admin@npa.gov.zw | password123 | npa-system-admin |
| ZRP Admin | admin@zrp.gov.zw | password123 | zrp-system-admin |
| Judicial Admin | admin@judiciary.gov.zw | password123 | judicial-system-admin |
| Courts Admin | courts@judiciary.gov.zw | password123 | judicial-courts-admin |

## 🎯 **WHAT EACH ADMIN CAN DO WHEN LOGGED IN**

- **Super Admin:** Access everything including system settings and managing other admins
- **Institution Admins:** Limited to their institution's data and specific functions
- **All Admins:** Can view audit logs and manage notifications

## 🔒 **SECURITY NOTES**

- Each admin is restricted to their institution's data
- Super Admin (COB) has cross-institution access
- All actions are logged for audit trails
- Passwords are hashed in the database

**Ready to test! Login with any of these credentials to see the role-based access in action!** 🎉