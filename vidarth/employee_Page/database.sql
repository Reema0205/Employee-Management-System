CREATE DATABASE IF NOT EXISTS employee_db;

USE employee_db;

CREATE TABLE IF NOT EXISTS employees (
    id         VARCHAR(10)  PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    role       VARCHAR(100) NOT NULL,
    dept       VARCHAR(50)  NOT NULL,
    salary     INT          NOT NULL DEFAULT 0,
    status     ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
    email      VARCHAR(100),
    phone      VARCHAR(20)
);

INSERT INTO employees (id, name, role, dept, salary, status, email, phone) VALUES
('EMP001', 'Mohamad Aollam',  'Software Engineer', 'IT', 70000, 'Active',   'aollam@company.com',  '0771234567'),
('EMP002', 'Raja Ravi',       'Software Engineer', 'IT', 60000, 'Active',   'raja@company.com',    '0772345678'),
('EMP003', 'Mohamad Akees',   'Software Engineer', 'IT', 80000, 'Inactive', 'akees@company.com',   '0773456789'),
('EMP004', 'Mohamad Hamyan',  'Software Engineer', 'IT', 60000, 'Active',   'hamyan@company.com',  '0774567890'),
('EMP005', 'Mohamad Afzal',   'Software Engineer', 'IT', 40000, 'Active',   'afzal@company.com',   '0775678901'),
('EMP006', 'Mohamad Aflan',   'Software Engineer', 'IT', 65000, 'Active',   'aflan@company.com',   '0776789012'),
('EMP007', 'Mohamad Asnaf',   'Software Engineer', 'IT', 62000, 'Active',   'asnaf@company.com',   '0777890123');
