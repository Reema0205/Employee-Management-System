-- ============================================================
--  medicore.sql — Medi Core Database Setup
--  Import this file in phpMyAdmin:
--    phpMyAdmin → Import → Choose File → medicore.sql → Go
--  OR run in MySQL CLI:
--    mysql -u root -p < medicore.sql
-- ============================================================

-- Create and select the database
CREATE DATABASE IF NOT EXISTS medicore
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE medicore;

-- ──────────────────────────────────────────────────────────
--  TABLE: doctors
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS doctors (
    id              INT           AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(150)  NOT NULL,
    specialty       VARCHAR(100)  NOT NULL,
    experience      INT           DEFAULT 0,
    rating          DECIMAL(3,1)  DEFAULT 0.0,
    fee_in_person   INT           DEFAULT 0,
    fee_virtual     INT           DEFAULT 0,
    avatar          INT           DEFAULT 0,
    bio             TEXT,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ──────────────────────────────────────────────────────────
--  TABLE: patients
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS patients (
    id          INT           AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150)  NOT NULL,
    email       VARCHAR(200)  DEFAULT '',
    phone       VARCHAR(30)   DEFAULT '',
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ──────────────────────────────────────────────────────────
--  TABLE: bookings
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS bookings (
    id              INT            AUTO_INCREMENT PRIMARY KEY,
    doctor_id       INT            NOT NULL,
    patient_id      INT            NOT NULL,
    consult_type    VARCHAR(20)    DEFAULT 'In-Person',
    booking_date    DATE,
    time_slot       VARCHAR(30)    DEFAULT '',
    symptoms        TEXT,
    amount          DECIMAL(10,2)  DEFAULT 0.00,
    created_at      TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (doctor_id)  REFERENCES doctors(id)  ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ──────────────────────────────────────────────────────────
--  SAMPLE DATA — doctors (optional, delete if not needed)
-- ──────────────────────────────────────────────────────────
INSERT INTO doctors (name, specialty, experience, rating, fee_in_person, fee_virtual, avatar, bio) VALUES
('Dr. Aisha Perera',    'Cardiologist',      12, 4.9, 3500, 2000, 0, 'Senior cardiologist with 12 years of clinical experience in cardiac care and interventional procedures.'),
('Dr. Rohan Silva',     'General Physician',  8, 4.7, 2000, 1200, 1, 'Experienced GP focused on preventive care, chronic disease management, and patient wellness.'),
('Dr. Nisha Fernando',  'Pediatrician',      10, 4.8, 2500, 1500, 2, 'Dedicated pediatrician specializing in child development, immunization, and neonatal care.'),
('Dr. Kasun Jayawardena','Dermatologist',     6, 4.6, 3000, 1800, 3, 'Specialist in skin disorders, cosmetic dermatology, and laser treatments.'),
('Dr. Malini Wijesekara','Psychiatrist',      14, 4.9, 4000, 2500, 4, 'Board-certified psychiatrist with expertise in anxiety, depression, and cognitive behavioral therapy.');

-- ──────────────────────────────────────────────────────────
--  SAMPLE DATA — patients (optional)
-- ──────────────────────────────────────────────────────────
INSERT INTO patients (name, email, phone) VALUES
('Fathima Sajeefa',  'fathima@example.com',  '+94 77 123 4567'),
('Nimal Perera',     'nimal@example.com',    '+94 71 987 6543'),
('Sathya Kumar',     'sathya@example.com',   '+94 76 555 1234');

-- ──────────────────────────────────────────────────────────
--  SAMPLE DATA — bookings (optional)
-- ──────────────────────────────────────────────────────────
INSERT INTO bookings (doctor_id, patient_id, consult_type, booking_date, time_slot, symptoms, amount) VALUES
(1, 1, 'In-Person', '2026-06-15', '09:00 AM', 'Chest pain, shortness of breath', 3500.00),
(2, 2, 'Virtual',   '2026-06-16', '11:30 AM', 'Fever and headache for 3 days',   1200.00),
(3, 3, 'In-Person', '2026-06-17', '02:00 PM', 'Child vaccination appointment',   2500.00);
