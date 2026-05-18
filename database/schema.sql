SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS password_reset_requests;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS billing_items;
DROP TABLE IF EXISTS billing;
DROP TABLE IF EXISTS prescription_status_log;
DROP TABLE IF EXISTS prescription_items;
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS inventory;
DROP TABLE IF EXISTS doctor_schedules;
DROP TABLE IF EXISTS doctors;
DROP TABLE IF EXISTS staff;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;


CREATE TABLE users (
    user_id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name     VARCHAR(100) NOT NULL,
    last_name      VARCHAR(100) NOT NULL,
    email          VARCHAR(255) NOT NULL,
    phone          VARCHAR(20)  DEFAULT NULL,
    password_hash  VARCHAR(255) NOT NULL,
    role           ENUM('admin', 'doctor', 'receptionist', 'patient') NOT NULL,
    is_active      TINYINT(1)   NOT NULL DEFAULT 1,
    must_change_password TINYINT(1) NOT NULL DEFAULT 0,
    last_login_at  DATETIME     DEFAULT NULL,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    UNIQUE KEY uniq_users_email (email),
    KEY idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE patients (
    patient_id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED NOT NULL,
    dob             DATE         NOT NULL,
    gender          ENUM('Male', 'Female', 'Other') NOT NULL,
    address         TEXT         DEFAULT NULL,
    insurance_info  VARCHAR(255) DEFAULT NULL,
    medical_history TEXT         DEFAULT NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (patient_id),
    UNIQUE KEY uniq_patients_user (user_id),
    CONSTRAINT fk_patients_user FOREIGN KEY (user_id)
        REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE doctors (
    doctor_id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id          INT UNSIGNED NOT NULL,
    specialization   VARCHAR(150) NOT NULL DEFAULT 'General Practitioner',
    license_number   VARCHAR(50)  DEFAULT NULL,
    consultation_fee DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (doctor_id),
    UNIQUE KEY uniq_doctors_user (user_id),
    UNIQUE KEY uniq_doctors_license (license_number),
    CONSTRAINT fk_doctors_user FOREIGN KEY (user_id)
        REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE staff (
    staff_id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED NOT NULL,
    position        VARCHAR(100) NOT NULL DEFAULT 'Receptionist',
    employee_number VARCHAR(50)  DEFAULT NULL,
    hire_date       DATE         DEFAULT NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (staff_id),
    UNIQUE KEY uniq_staff_user (user_id),
    UNIQUE KEY uniq_staff_employee_no (employee_number),
    CONSTRAINT fk_staff_user FOREIGN KEY (user_id)
        REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE doctor_schedules (
    schedule_id  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    doctor_id    INT UNSIGNED NOT NULL,
    day_of_week  ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
    start_time   TIME         NOT NULL,
    end_time     TIME         NOT NULL,
    is_active    TINYINT(1)   NOT NULL DEFAULT 1,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (schedule_id),
    UNIQUE KEY uniq_doctor_day (doctor_id, day_of_week),
    CONSTRAINT fk_schedule_doctor FOREIGN KEY (doctor_id)
        REFERENCES doctors (doctor_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE inventory (
    inventory_id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    item_name         VARCHAR(200) NOT NULL,
    generic_name      VARCHAR(200) DEFAULT NULL,
    description       TEXT         DEFAULT NULL,
    unit              VARCHAR(50)  NOT NULL DEFAULT 'tablet',
    quantity          INT UNSIGNED NOT NULL DEFAULT 0,
    minimum_threshold INT UNSIGNED NOT NULL DEFAULT 10,
    unit_price        DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    expiration_date   DATE         DEFAULT NULL,
    is_active         TINYINT(1)   NOT NULL DEFAULT 1,
    created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (inventory_id),
    KEY idx_inventory_item_name (item_name),
    KEY idx_inventory_expiration (expiration_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE appointments (
    appointment_id     INT UNSIGNED NOT NULL AUTO_INCREMENT,
    patient_id         INT UNSIGNED NOT NULL,
    doctor_id          INT UNSIGNED NOT NULL,
    appointment_date   DATETIME     NOT NULL,
    duration_minutes   INT UNSIGNED NOT NULL DEFAULT 30,
    reason             VARCHAR(500) DEFAULT NULL,
    status             ENUM('scheduled', 'completed', 'cancelled', 'no_show') NOT NULL DEFAULT 'scheduled',
    booked_by_user_id  INT UNSIGNED DEFAULT NULL,
    consultation_notes TEXT         DEFAULT NULL,
    created_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at         DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (appointment_id),
    KEY idx_appt_patient (patient_id),
    KEY idx_appt_doctor (doctor_id),
    KEY idx_appt_date (appointment_date),
    KEY idx_appt_doctor_date (doctor_id, appointment_date),
    CONSTRAINT fk_appt_patient FOREIGN KEY (patient_id)
        REFERENCES patients (patient_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_appt_doctor FOREIGN KEY (doctor_id)
        REFERENCES doctors (doctor_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_appt_booked_by FOREIGN KEY (booked_by_user_id)
        REFERENCES users (user_id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE prescriptions (
    prescription_id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    appointment_id       INT UNSIGNED NOT NULL,
    patient_id           INT UNSIGNED NOT NULL,
    doctor_id            INT UNSIGNED NOT NULL,
    dispensed_by_user_id INT UNSIGNED DEFAULT NULL,
    status               ENUM('pending', 'approved', 'dispensed', 'alternative_requested', 'cancelled') NOT NULL DEFAULT 'pending',
    diagnosis            VARCHAR(500) DEFAULT NULL,
    notes                TEXT         DEFAULT NULL,
    issued_at            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dispensed_at         DATETIME     DEFAULT NULL,
    created_at           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at           DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (prescription_id),
    KEY idx_rx_appointment (appointment_id),
    KEY idx_rx_patient (patient_id),
    KEY idx_rx_doctor (doctor_id),
    KEY idx_rx_status (status),
    CONSTRAINT fk_rx_appointment FOREIGN KEY (appointment_id)
        REFERENCES appointments (appointment_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_rx_patient FOREIGN KEY (patient_id)
        REFERENCES patients (patient_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_rx_doctor FOREIGN KEY (doctor_id)
        REFERENCES doctors (doctor_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_rx_dispensed_by FOREIGN KEY (dispensed_by_user_id)
        REFERENCES users (user_id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE prescription_items (
    item_id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    prescription_id     INT UNSIGNED NOT NULL,
    inventory_id        INT UNSIGNED NOT NULL,
    dosage              VARCHAR(100) NOT NULL,
    frequency           VARCHAR(100) NOT NULL,
    duration_days       INT UNSIGNED NOT NULL DEFAULT 1,
    quantity            INT UNSIGNED NOT NULL,
    unit_price_at_issue DECIMAL(10, 2) NOT NULL,
    is_substituted      TINYINT(1)   NOT NULL DEFAULT 0,
    substitution_notes  VARCHAR(500) DEFAULT NULL,
    PRIMARY KEY (item_id),
    KEY idx_rx_item_prescription (prescription_id),
    KEY idx_rx_item_inventory (inventory_id),
    CONSTRAINT fk_rx_item_prescription FOREIGN KEY (prescription_id)
        REFERENCES prescriptions (prescription_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_rx_item_inventory FOREIGN KEY (inventory_id)
        REFERENCES inventory (inventory_id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE prescription_status_log (
    log_id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    prescription_id    INT UNSIGNED NOT NULL,
    changed_by_user_id INT UNSIGNED NOT NULL,
    old_status         VARCHAR(50)  DEFAULT NULL,
    new_status         VARCHAR(50)  NOT NULL,
    comment            VARCHAR(500) DEFAULT NULL,
    changed_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (log_id),
    KEY idx_rx_log_prescription (prescription_id),
    CONSTRAINT fk_rx_log_prescription FOREIGN KEY (prescription_id)
        REFERENCES prescriptions (prescription_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_rx_log_user FOREIGN KEY (changed_by_user_id)
        REFERENCES users (user_id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE billing (
    billing_id     INT UNSIGNED NOT NULL AUTO_INCREMENT,
    patient_id     INT UNSIGNED NOT NULL,
    appointment_id INT UNSIGNED DEFAULT NULL,
    invoice_number VARCHAR(50)  NOT NULL,
    total_amount   DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    amount_paid    DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    payment_status ENUM('pending', 'partially_paid', 'paid', 'cancelled') NOT NULL DEFAULT 'pending',
    payment_method ENUM('cash', 'card', 'eft', 'medical_aid', 'other') DEFAULT NULL,
    billing_date   DATE         NOT NULL DEFAULT (CURRENT_DATE),
    paid_date      DATETIME     DEFAULT NULL,
    notes          TEXT         DEFAULT NULL,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (billing_id),
    UNIQUE KEY uniq_billing_invoice (invoice_number),
    KEY idx_billing_patient (patient_id),
    KEY idx_billing_status (payment_status),
    CONSTRAINT fk_billing_patient FOREIGN KEY (patient_id)
        REFERENCES patients (patient_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_billing_appointment FOREIGN KEY (appointment_id)
        REFERENCES appointments (appointment_id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE billing_items (
    item_id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    billing_id           INT UNSIGNED NOT NULL,
    item_type            ENUM('consultation', 'medication', 'procedure', 'other') NOT NULL,
    description          VARCHAR(255) NOT NULL,
    prescription_item_id INT UNSIGNED DEFAULT NULL,
    inventory_id         INT UNSIGNED DEFAULT NULL,
    quantity             INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price           DECIMAL(10, 2) NOT NULL,
    line_total           DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (item_id),
    KEY idx_billing_item_billing (billing_id),
    CONSTRAINT fk_billing_item_billing FOREIGN KEY (billing_id)
        REFERENCES billing (billing_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_billing_item_rx FOREIGN KEY (prescription_item_id)
        REFERENCES prescription_items (item_id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_billing_item_inventory FOREIGN KEY (inventory_id)
        REFERENCES inventory (inventory_id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE notifications (
    notification_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED NOT NULL,
    type            ENUM('appointment_reminder', 'prescription_ready', 'low_stock', 'expiry_warning', 'general') NOT NULL,
    title           VARCHAR(200) NOT NULL,
    message         TEXT         NOT NULL,
    related_table   VARCHAR(50)  DEFAULT NULL,
    related_id      INT UNSIGNED DEFAULT NULL,
    is_read         TINYINT(1)   NOT NULL DEFAULT 0,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    read_at         DATETIME     DEFAULT NULL,
    PRIMARY KEY (notification_id),
    KEY idx_notif_user (user_id),
    KEY idx_notif_unread (user_id, is_read),
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id)
        REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE password_reset_requests (
    request_id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id            INT UNSIGNED NOT NULL,
    email_submitted    VARCHAR(255) NOT NULL,
    status             ENUM('pending', 'completed', 'rejected', 'expired') NOT NULL DEFAULT 'pending',
    requested_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    handled_by_user_id INT UNSIGNED DEFAULT NULL,
    handled_at         DATETIME     DEFAULT NULL,
    temp_password_set  TINYINT(1)   NOT NULL DEFAULT 0,
    notes              VARCHAR(500) DEFAULT NULL,
    PRIMARY KEY (request_id),
    KEY idx_pwreset_user (user_id),
    KEY idx_pwreset_status (status),
    CONSTRAINT fk_pwreset_user FOREIGN KEY (user_id)
        REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_pwreset_handler FOREIGN KEY (handled_by_user_id)
        REFERENCES users (user_id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
