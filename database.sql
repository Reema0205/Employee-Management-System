-- ================================================================
--  IPS Company Panel — Full Database Setup
--  Import via phpMyAdmin or: mysql -u root -p < database.sql
-- ================================================================

CREATE DATABASE IF NOT EXISTS ips_company_panel
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ips_company_panel;

-- ──────────────────────────────────────────────────────────────
--  TABLE: companies
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS companies (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    phone      VARCHAR(30)  NOT NULL DEFAULT '',
    logo       VARCHAR(255) NOT NULL DEFAULT '',
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────
--  TABLE: jobs
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS jobs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    company_id  INT NOT NULL,
    title       VARCHAR(200) NOT NULL,
    type        ENUM('Full Time','Part Time','Internship','Remote') NOT NULL,
    location    VARCHAR(100) NOT NULL,
    experience  VARCHAR(50)  NOT NULL,
    description TEXT         NOT NULL,
    skills      VARCHAR(300) NOT NULL DEFAULT '',
    status      ENUM('active','closed') NOT NULL DEFAULT 'active',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────
--  TABLE: applicants
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS applicants (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    job_id     INT NOT NULL,
    name       VARCHAR(150) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    experience VARCHAR(50)  NOT NULL DEFAULT '',
    resume     VARCHAR(255) NOT NULL DEFAULT '',
    status     ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
    applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id)     REFERENCES jobs(id)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────
--  TABLE: shortlisted
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS shortlisted (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    company_id   INT NOT NULL,
    applicant_id INT NOT NULL,
    added_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_shortlist (company_id, applicant_id),
    FOREIGN KEY (company_id)   REFERENCES companies(id)  ON DELETE CASCADE,
    FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────
--  TABLE: interviews
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS interviews (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    company_id     INT NOT NULL,
    applicant_id   INT NOT NULL,
    interview_date DATE NOT NULL,
    interview_time TIME NOT NULL,
    mode           ENUM('Online','Offline') NOT NULL DEFAULT 'Online',
    meeting_link   VARCHAR(255) NOT NULL DEFAULT '',
    status         ENUM('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id)   REFERENCES companies(id)  ON DELETE CASCADE,
    FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────
--  TABLE: messages
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS messages (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    company_id   INT NOT NULL,
    applicant_id INT NOT NULL,
    sender       ENUM('company','applicant') NOT NULL,
    message      TEXT NOT NULL,
    sent_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id)   REFERENCES companies(id)  ON DELETE CASCADE,
    FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
--  SEED DATA
--  Admin: email = admin  |  password = Admin123
--  (bcrypt $2y$10$ hash of "Admin123")
-- ================================================================

INSERT INTO companies (name, email, password, phone) VALUES
('IPS Admin', 'admin', '$2y$10$4hcTHm6y.ok20D3MML5SuejMEVDywSWm6hQ9t1u5AwvKeWH/IC0l.', '+94 77 000 0000');

-- Sample jobs (company_id = 1)
INSERT INTO jobs (company_id, title, type, location, experience, description, skills, status) VALUES
(1, 'Frontend Developer',  'Full Time',  'Chennai',   '1-3 Years', 'Build responsive web UIs using HTML, CSS, JavaScript and React.',       'HTML, CSS, JS, React',   'active'),
(1, 'UI/UX Designer',      'Full Time',  'Bangalore', '2-4 Years', 'Design user-centred interfaces and conduct usability research.',         'Figma, Adobe XD, Sketch','closed'),
(1, 'React Developer',     'Internship', 'Remote',    'Fresher',   'Build and maintain React components for our SaaS platform.',            'React, Redux, REST APIs','active'),
(1, 'Backend Developer',   'Full Time',  'Colombo',   '3-5 Years', 'Design and build RESTful APIs with Node.js and MySQL.',                 'Node.js, Express, MySQL','active'),
(1, 'Mobile App Developer','Part Time',  'Remote',    '1-2 Years', 'Develop cross-platform mobile apps using Flutter or React Native.',     'Flutter, React Native',  'active');

-- Sample applicants (company_id = 1)
INSERT INTO applicants (company_id, job_id, name, email, experience, status) VALUES
(1, 1, 'John Smith',          'john@gmail.com',    '2 Years', 'pending'),
(1, 2, 'Emma Watson',         'emma@gmail.com',    '3 Years', 'accepted'),
(1, 3, 'Michael Brown',       'michael@gmail.com', '1 Year',  'accepted'),
(1, 1, 'Sarah Johnson',       'sarah@gmail.com',   '4 Years', 'rejected'),
(1, 2, 'Kavindu Perera',      'kavindu@gmail.com', '1 Year',  'pending'),
(1, 3, 'Nimasha Fernando',    'nimasha@gmail.com', 'Fresher', 'accepted'),
(1, 1, 'Hashila Jayawardena', 'hashila@gmail.com', '2 Years', 'accepted');

-- Shortlisted (accepted applicants)
INSERT INTO shortlisted (company_id, applicant_id) VALUES
(1,2),(1,3),(1,6),(1,7);

-- Interviews
INSERT INTO interviews (company_id, applicant_id, interview_date, interview_time, mode, status) VALUES
(1, 2, '2026-07-10', '10:00:00', 'Online',  'scheduled'),
(1, 3, '2026-07-11', '14:00:00', 'Online',  'scheduled'),
(1, 6, '2026-07-12', '11:30:00', 'Offline', 'scheduled'),
(1, 7, '2026-07-13', '09:00:00', 'Online',  'scheduled');

-- Messages
INSERT INTO messages (company_id, applicant_id, sender, message) VALUES
(1, 2, 'applicant', 'Hello, I applied for UI/UX Designer position.'),
(1, 2, 'company',   'Thank you Emma! Your application looks great.'),
(1, 2, 'applicant', 'When can I expect the next update?'),
(1, 2, 'company',   'We will schedule an interview soon. Please stay tuned.'),
(1, 3, 'applicant', 'Hi, I am interested in the React Developer internship.'),
(1, 3, 'company',   'Great! We have reviewed your profile and shortlisted you.');
