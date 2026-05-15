-- ============================================================
-- MedicOM Clinic Management System
-- STARTER SCHEMA (Authentication Only)
-- ============================================================
-- This is the MINIMUM schema required for login/register to work.
-- The full schema (appointments, prescriptions, billing, inventory)
-- will be designed in the Saturday team session.
--
-- To use:
--   1. Open phpMyAdmin (http://localhost/phpmyadmin)
--   2. Create a new database called 'medicom_db' (utf8mb4_unicode_ci)
--   3. Select it, go to Import, choose this file, click Go.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- USERS TABLE
-- Single table holding all login credentials regardless of role.
-- Role-specific data lives in extended tables (patients, doctors, etc.)
-- ============================================================
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    user_id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name     VARCHAR(100) NOT NULL,
    last_name      VARCHAR(100) NOT NULL,
    email          VARCHAR(255) NOT NULL,
    phone          VARCHAR(20)  DEFAULT NULL,
    password_hash  VARCHAR(255) NOT NULL,
    role           ENUM('admin', 'doctor', 'receptionist', 'patient') NOT NULL,
    is_active      TINYINT(1)   NOT NULL DEFAULT 1,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    UNIQUE KEY uniq_users_email (email),
    KEY idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PATIENTS TABLE
-- Extends users with patient-specific fields.
-- Add more columns (address, insurance_info, BMI, etc.) on Saturday.
-- ============================================================
DROP TABLE IF EXISTS patients;
CREATE TABLE patients (
    patient_id     INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id        INT UNSIGNED NOT NULL,
    dob            DATE         NOT NULL,
    gender         ENUM('Male', 'Female', 'Other') NOT NULL,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (patient_id),
    UNIQUE KEY uniq_patient_user (user_id),
    CONSTRAINT fk_patient_user FOREIGN KEY (user_id)
        REFERENCES users (user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DOCTORS TABLE
-- Stub for doctor profiles. Expand on Saturday.
-- ============================================================
DROP TABLE IF EXISTS doctors;
CREATE TABLE doctors (
    doctor_id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id        INT UNSIGNED NOT NULL,
    specialization VARCHAR(150) DEFAULT NULL,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (doctor_id),
    UNIQUE KEY uniq_doctor_user (user_id),
    CONSTRAINT fk_doctor_user FOREIGN KEY (user_id)
        REFERENCES users (user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED DATA — Test accounts for development
-- ============================================================
-- All test passwords are 'Password123'
-- Hash generated with: password_hash('Password123', PASSWORD_DEFAULT)
-- You can regenerate by running this in PHP:
--   echo password_hash('Password123', PASSWORD_DEFAULT);

INSERT INTO users (first_name, last_name, email, phone, password_hash, role, is_active) VALUES
('Admin',     'User',    'admin@medicom.test',        '0712345001', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'admin',        1),
('Dr. John',  'Smith',   'dr.smith@medicom.test',     '0712345002', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'doctor',       1),
('Sarah',     'Jones',   'reception@medicom.test',    '0712345003', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'receptionist', 1),
('Jane',      'Patient', 'patient@medicom.test',      '0712345004', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'patient',      1);

-- Link the patient record to the patient user
INSERT INTO patients (user_id, dob, gender) VALUES
((SELECT user_id FROM users WHERE email = 'patient@medicom.test'), '1990-05-15', 'Female');

-- Link the doctor record to the doctor user
INSERT INTO doctors (user_id, specialization) VALUES
((SELECT user_id FROM users WHERE email = 'dr.smith@medicom.test'), 'General Practitioner');

-- ============================================================
-- TEST CREDENTIALS
-- ============================================================
-- Admin:        admin@medicom.test        / Password123
-- Doctor:       dr.smith@medicom.test     / Password123
-- Receptionist: reception@medicom.test    / Password123
-- Patient:      patient@medicom.test      / Password123
-- ============================================================
