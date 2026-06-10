CREATE DATABASE IF NOT EXISTS payroll_db;

USE payroll_db;

CREATE TABLE IF NOT EXISTS employees (
    id        VARCHAR(10)  PRIMARY KEY,
    name      VARCHAR(100) NOT NULL,
    dept      VARCHAR(50)  NOT NULL,
    basic     INT          NOT NULL DEFAULT 0,
    bonus     INT          NOT NULL DEFAULT 0,
    deduct    INT          NOT NULL DEFAULT 0
);

INSERT INTO employees (id, name, dept, basic, bonus, deduct) VALUES
('EMP001', 'Mohamad Aollam', 'Engineering', 60000, 5000, 3000),
('EMP002', 'Raja Ravi',      'HR',          60000, 3000, 2000),
('EMP003', 'Mohamad Akees',  'Finance',     65000, 4000, 2500),
('EMP004', 'Mohamad Hamyan', 'Engineering', 75000, 4000, 2000),
('EMP005', 'Mohamad Afzal',  'Marketing',   55000, 2500, 1500);
