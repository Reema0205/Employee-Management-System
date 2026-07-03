# IPS Company Panel — Setup Guide

A complete **PHP + MySQL** company recruitment panel.

---

## ✅ Default Admin Credentials

| Field              | Value      |
|--------------------|------------|
| Username / Email   | `admin`    |
| Password           | `Admin123` |

---

## Quick Setup (3 Steps)

### Step 1 — Import Database
Run `database.sql` in phpMyAdmin → SQL tab, or via CLI:
```bash
mysql -u root -p < database.sql
```

### Step 2 — Set DB Credentials
Edit **`includes/db.php`**:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');   // your MySQL username
define('DB_PASS', '');       // your MySQL password
define('DB_NAME', 'ips_company_panel');
```

### Step 3 — Create Admin Account
Open in browser:
```
http://localhost/ips-company-panel/admin_credential.php
```
Click **"Create with Default Credentials"** → admin account is created instantly.

Then go to:
```
http://localhost/ips-company-panel/login.php
```
Login with `admin` / `Admin123`

> ⚠ **Delete `admin_credential.php`** after setup!

---

## Local Server Setup

| Server   | Folder path                              |
|----------|------------------------------------------|
| XAMPP    | `C:/xampp/htdocs/ips-company-panel/`     |
| WAMP     | `C:/wamp64/www/ips-company-panel/`       |
| MAMP     | `/Applications/MAMP/htdocs/ips-company-panel/` |
| Linux    | `/var/www/html/ips-company-panel/`       |

---

## File Structure

```
ips-company-panel/
├── index.php                ← Entry (redirects to login or dashboard)
├── login.php                ← Login page (username or email)
├── dashboard.php            ← Stats + chart
├── post-job.php             ← Post new job
├── my-jobs.php              ← Manage jobs (edit/delete/toggle)
├── applicants.php           ← Accept/reject applicants
├── shortlisted.php          ← Shortlisted candidates
├── interviews.php           ← Schedule/cancel interviews
├── messages.php             ← Messaging system
├── settings.php             ← Update profile & password
├── logout.php               ← Logout confirmation
├── admin_credential.php     ← ONE-TIME setup tool (delete after use)
├── database.sql             ← Full DB schema + seed data
│
├── includes/
│   ├── db.php               ← DB connection (edit credentials here)
│   ├── auth.php             ← Session helpers
│   ├── sidebar.php          ← Shared sidebar
│   └── topbar.php           ← Shared topbar
│
├── css/                     ← All page stylesheets
└── uploads/                 ← Resume file uploads (chmod 755)
```

---

## Features

| Page          | Features                                                     |
|---------------|--------------------------------------------------------------|
| Login         | Username or email login, bcrypt password verify              |
| Dashboard     | Live stat cards + bar chart from DB                          |
| Post a Job    | Form → saves to `jobs` table                                 |
| My Jobs       | Edit (modal), delete, active/close toggle — all AJAX         |
| Applicants    | Accept/reject → updates DB, auto-shortlists accepted         |
| Shortlisted   | Remove from list, schedule interview button                  |
| Interviews    | Schedule form → DB, cancel, delete — all AJAX                |
| Messages      | Real send/receive stored in `messages` table                 |
| Settings      | Update name/username/phone + change password                 |
| Logout        | Session destroy + confirmation page                          |
| admin_credential | One-click default creds (admin/Admin123) or custom form  |
