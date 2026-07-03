-- ============================================================
--  database.sql  —  THE ONLY SQL FILE FOR THIS PROJECT
--  Import this ONE file (phpMyAdmin > Import, or:
--      mysql -u root -p < database.sql
--  ) and every module (Login, Employees, Payroll, Attendance,
--  Leave, Reports/KPI) will work with a single database: ems_db
-- ============================================================

CREATE DATABASE IF NOT EXISTS ems_db;
USE ems_db;

-- ------------------------------------------------------------
-- 1. USERS  (login / logout for the dashboard)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(100) DEFAULT NULL,
    password   VARCHAR(255) NOT NULL,
    role       VARCHAR(30)  NOT NULL DEFAULT 'Admin',
    fullname   VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Default login  ->  username: admin   password: admin123
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@ems.com', '$2y$10$FpIaBgWs5WQhrg3nSGJcDukzPfsdhYs7tks2kVaMlmFl.9fadnoc6', 'Admin')
ON DUPLICATE KEY UPDATE username = username;
-- (the hash above is bcrypt for "admin123")

-- ------------------------------------------------------------
-- 2. EMPLOYEES  (used by Employees page + Payroll page)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS employees (
    id      VARCHAR(10)  PRIMARY KEY,
    name    VARCHAR(100) NOT NULL,
    role    VARCHAR(100) DEFAULT '',
    dept    VARCHAR(50)  NOT NULL,
    salary  INT          NOT NULL DEFAULT 0,
    status  ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
    email   VARCHAR(100) DEFAULT '',
    phone   VARCHAR(20)  DEFAULT '',
    basic   INT          NOT NULL DEFAULT 0,
    bonus   INT          NOT NULL DEFAULT 0,
    deduct  INT          NOT NULL DEFAULT 0
);

INSERT INTO employees (id, name, role, dept, salary, status, email, phone, basic, bonus, deduct) VALUES
('EMP001', 'Mohamad Aollam', 'Software Engineer', 'IT',        70000, 'Active',   'aollam@company.com', '0771234567', 60000, 5000, 3000),
('EMP002', 'Raja Ravi',      'Software Engineer', 'HR',        60000, 'Active',   'raja@company.com',   '0772345678', 60000, 3000, 2000),
('EMP003', 'Mohamad Akees',  'Software Engineer', 'Finance',   80000, 'Inactive', 'akees@company.com',  '0773456789', 65000, 4000, 2500),
('EMP004', 'Mohamad Hamyan', 'Software Engineer', 'IT',        60000, 'Active',   'hamyan@company.com', '0774567890', 75000, 4000, 2000),
('EMP005', 'Mohamad Afzal',  'Software Engineer', 'Marketing', 40000, 'Active',   'afzal@company.com',  '0775678901', 55000, 2500, 1500),
('EMP006', 'Mohamad Aflan',  'Software Engineer', 'IT',        65000, 'Active',   'aflan@company.com',  '0776789012', 50000, 3000, 1800),
('EMP007', 'Mohamad Asnaf',  'Software Engineer', 'IT',        62000, 'Active',   'asnaf@company.com',  '0777890123', 48000, 2800, 1600)
ON DUPLICATE KEY UPDATE id = id;

-- ------------------------------------------------------------
-- 3. ATTENDANCE_EMPLOYEES  (QR registration - Attendance module)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS attendance_employees (
    emp_id     VARCHAR(50)  PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    age        INT          NOT NULL,
    email      VARCHAR(100) NOT NULL,
    phone      VARCHAR(20)  NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO attendance_employees (emp_id, name, age, email, phone, created_at) VALUES
('EMP001', 'Mohamad Aollam', 24, 'aollam@company.com', '0771234567', NOW()),
('EMP002', 'Raja Ravi',      26, 'raja@company.com',   '0772345678', NOW())
ON DUPLICATE KEY UPDATE emp_id = emp_id;

-- ------------------------------------------------------------
-- 4. ATTENDANCE_RECORDS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS attendance_records (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    emp_id          VARCHAR(50) NOT NULL,
    attendance_date DATE        NOT NULL,
    check_in        TIME        DEFAULT NULL,
    check_out       TIME        DEFAULT NULL,
    status          ENUM('Present','Late','Absent') DEFAULT 'Present',
    remarks         VARCHAR(255) DEFAULT 'On time',
    UNIQUE KEY unique_daily_attendance (emp_id, attendance_date),
    CONSTRAINT attendance_records_fk FOREIGN KEY (emp_id)
        REFERENCES attendance_employees (emp_id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- 5. PERFORMANCE_REVIEWS  (KPI module)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS performance_reviews (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    employee_name  VARCHAR(100)  NOT NULL,
    review_period  VARCHAR(20)   NOT NULL,
    kpi_score      DECIMAL(5,2)  NOT NULL,
    reviewed_by    VARCHAR(50)   NOT NULL,
    feedback       TEXT          DEFAULT NULL,
    review_date    DATE          NOT NULL
);

-- ------------------------------------------------------------
-- 6. LEAVES  (Leave Management module)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS leaves (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(20)  NOT NULL,
    leave_type  VARCHAR(50)  NOT NULL,
    start_date  DATE         NOT NULL,
    end_date    DATE         NOT NULL,
    reason      VARCHAR(255) DEFAULT '',
    status      VARCHAR(20)  NOT NULL DEFAULT 'Pending'
);
