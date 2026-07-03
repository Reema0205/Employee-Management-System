# Employee Management System — Setup Guide

## What was fixed
1. **One database connection file for the whole project: `db.php`** (project root).
   Every module (Employees, Payroll, Attendance, Leave, Login) now includes
   this single file instead of having its own separate DB credentials.
   The old per-folder `db.php` / `config.php` files are now tiny "shims"
   that just point to this one root file — so if you ever change your
   DB host/username/password, you only edit it in **one place**.

2. **One SQL file for the whole project: `database.sql`** (project root).
   Import only this file. It creates a single database `ems_db` with all
   the tables the project needs: `users`, `employees`, `attendance_employees`,
   `attendance_records`, `performance_reviews`, `leaves`.

3. **Dashboard sidebar links now actually work.** Clicking Employees,
   Attendance, Leave, Payroll, KPI, Reports, Settings takes you to the
   correct page in the correct folder. Every module page also has a small
   "⬅ Dashboard" button (top-left) to get back.

4. **Real Login / Logout.**
   - `login.html` → `login.php` checks the `users` table and starts a session.
   - Default login: **username `admin`, password `admin123`**
   - `index.php` (the dashboard) is now session-protected — if you're not
     logged in, it redirects you to `login.html`.
   - Clicking **Logout** in the sidebar calls `logout.php`, which destroys
     the session and sends you back to the login page.
   - "Forgot password" (`Reset.html`) is wired to `reset_password.php`.

## How to run it (XAMPP / WAMP / Laragon)
1. Copy the whole `Employee-Management-System` folder into your server's
   `htdocs` (XAMPP) or `www` (WAMP) directory.
2. Start Apache + MySQL.
3. Open **phpMyAdmin** → Import → choose `database.sql` → Go.
   (This creates the `ems_db` database with everything needed.)
4. If your MySQL root user has a password, or you don't use `root`,
   update the 4 lines at the top of `db.php`:
   ```php
   $DB_HOST = "localhost";
   $DB_USER = "root";
   $DB_PASS = "";
   $DB_NAME = "ems_db";
   ```
5. Visit `http://localhost/Employee-Management-System/login.html`
   Login with `admin` / `admin123`.

## Notes
- `attendance_page` was renamed from `akeela (Attendance page)` — spaces
  and brackets in folder names cause broken links/URLs in browsers, so it
  was renamed to a plain, safe name.
- The `Reema`, `Sanfara`, `vidarth` folders still contain each student's
  original individual files for reference — the **live, working app**
  now runs from the root-level files (`index.php`, `login.html`,
  `db.php`, `database.sql`, etc.) plus each module's page.
- `Reema/medi` is a separate hospital-booking mini project bundled in the
  zip by mistake — it isn't part of the Employee Management System and
  was left untouched.
