INSERT INTO users (user_id, first_name, last_name, email, phone, password_hash, role, is_active) VALUES
(1, 'Mandla',    'Khumalo',   'admin@medicom.test',       '0712340001', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'admin', 1),
(2, 'John',      'Smith',     'dr.smith@medicom.test',    '0712340002', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'doctor', 1),
(3, 'Priya',     'Naidoo',    'dr.naidoo@medicom.test',   '0712340003', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'doctor', 1),
(4, 'Sipho',     'Nkosi',     'dr.nkosi@medicom.test',    '0712340004', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'doctor', 1),
(5, 'Sarah',     'Jones',     'reception@medicom.test',   '0712340005', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'receptionist', 1),
(6, 'Thandi',    'Mokoena',   'reception2@medicom.test',  '0712340006', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'receptionist', 1),
(7,  'Jane',     'Patient',   'patient@medicom.test',     '0721340007', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'patient', 1),
(8,  'David',    'van Wyk',   'david.vw@example.com',     '0721340008', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'patient', 1),
(9,  'Lerato',   'Dlamini',   'lerato.d@example.com',     '0821340009', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'patient', 1),
(10, 'Mike',     'Pillay',    'mike.p@example.com',       '0821340010', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'patient', 1),
(11, 'Aisha',    'Hassan',    'aisha.h@example.com',      '0731340011', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'patient', 1),
(12, 'Peter',    'Botha',     'peter.b@example.com',      '0731340012', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'patient', 1),
(13, 'Nomvula',  'Zulu',      'nomvula.z@example.com',    '0641340013', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'patient', 1),
(14, 'Rakesh',   'Singh',     'rakesh.s@example.com',     '0641340014', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'patient', 1),
(15, 'Elize',    'du Toit',   'elize.dt@example.com',     '0721340015', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'patient', 1),
(16, 'Karabo',   'Mthembu',   'karabo.m@example.com',     '0721340016', '$2y$10$eB2lNT3ifD0M1PV7.3x.h.titDRLvu4Oir436ABwKMSyEm6XBSbZm', 'patient', 1);


INSERT INTO patients (user_id, dob, gender, address, insurance_info, medical_history) VALUES
(7,  '1990-05-15', 'Female', '12 Florida Rd, Durban',         'Discovery Health Smart',    'Mild asthma, no known allergies'),
(8,  '1978-03-22', 'Male',   '45 Musgrave Rd, Berea',         'Bonitas Standard',          'Hypertension, on lisinopril'),
(9,  '1995-11-08', 'Female', '78 Umhlanga Rocks Dr, Umhlanga','Momentum Health Custom',    'No chronic conditions'),
(10, '1982-07-30', 'Male',   '23 Glenwood Ave, Glenwood',     NULL,                        'Type 2 diabetes, controlled'),
(11, '2001-02-14', 'Female', '156 Ridge Rd, Morningside',     'Medihelp Plus',             'Allergic to penicillin'),
(12, '1965-09-19', 'Male',   '34 Berea Rd, Berea',            'GEMS Onyx',                 'Heart condition, post-bypass 2020'),
(13, '1988-12-03', 'Female', '67 Innes Rd, Greyville',        'Discovery Health KeyCare',  'Pregnant - 24 weeks'),
(14, '1973-04-25', 'Male',   '90 NMR Ave, Stamford Hill',     'Bonitas BonComplete',       'High cholesterol, on statins'),
(15, '1996-08-11', 'Female', '5 Lenny Naidu Dr, Cato Manor',  NULL,                        'Migraines, occasional'),
(16, '2010-01-20', 'Male',   '88 Sydney Rd, Congella',        'Discovery Health Classic',  'Healthy child, regular checkups');


INSERT INTO doctors (user_id, specialization, license_number, consultation_fee) VALUES
(2, 'General Practitioner', 'MP123456', 450.00),
(3, 'Paediatrician',        'MP234567', 650.00),
(4, 'Cardiologist',         'MP345678', 850.00);


INSERT INTO staff (user_id, position, employee_number, hire_date) VALUES
(5, 'Senior Receptionist', 'EMP001', '2023-01-15'),
(6, 'Receptionist',        'EMP002', '2024-06-01');


INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time) VALUES
(1, 'Monday',    '08:00:00', '16:00:00'),
(1, 'Tuesday',   '08:00:00', '16:00:00'),
(1, 'Wednesday', '08:00:00', '16:00:00'),
(1, 'Thursday',  '08:00:00', '16:00:00'),
(1, 'Friday',    '08:00:00', '13:00:00'),
(2, 'Monday',    '09:00:00', '17:00:00'),
(2, 'Wednesday', '09:00:00', '17:00:00'),
(2, 'Friday',    '09:00:00', '15:00:00'),
(3, 'Tuesday',   '10:00:00', '18:00:00'),
(3, 'Thursday',  '10:00:00', '18:00:00');


INSERT INTO inventory (item_name, generic_name, description, unit, quantity, minimum_threshold, unit_price, expiration_date) VALUES
('Panado',           'Paracetamol 500mg',    'Pain and fever relief',           'tablet', 500, 50,  1.20,  '2027-08-31'),
('Disprin',          'Aspirin 300mg',        'Pain relief, anti-inflammatory',  'tablet', 300, 50,  1.50,  '2026-12-15'),
('Brufen',           'Ibuprofen 200mg',      'Anti-inflammatory',               'tablet', 280, 40,  2.10,  '2027-04-20'),
('Amoxil',           'Amoxicillin 500mg',    'Broad-spectrum antibiotic',       'capsule',200, 30,  4.80,  '2026-11-10'),
('Lipitor',          'Atorvastatin 20mg',    'Cholesterol management',          'tablet', 150, 20,  6.50,  '2027-02-28'),
('Glucophage',       'Metformin 500mg',      'Type 2 diabetes management',      'tablet', 240, 30,  3.20,  '2027-06-30'),
('Ventolin Inhaler', 'Salbutamol',           'Asthma rescue inhaler',           'inhaler',  8, 10,  85.00, '2026-09-15'),
('Adco-Zolpidem',    'Zolpidem 10mg',        'Sleep aid',                       'tablet',  12, 20,  4.00,  '2027-01-20'),
('Cipro',            'Ciprofloxacin 500mg',  'Antibiotic for UTIs',             'tablet',  60, 30,  5.20,  '2026-06-05'),
('Augmentin',        'Amoxicillin/Clavulanate','Antibiotic for ear/sinus',     'tablet',  90, 25,  7.80,  '2026-06-10'),
('Adco-Dol',         'Paracetamol/Codeine',  'Strong pain relief',              'tablet', 100, 20,  3.50,  '2027-09-30'),
('Voltaren Gel',     'Diclofenac topical',   'Topical anti-inflammatory',       'tube',    45, 10, 65.00,  '2027-03-15'),
('Eltroxin',         'Levothyroxine 50mcg',  'Thyroid hormone replacement',     'tablet', 180, 30,  2.40,  '2027-07-20'),
('Norvasc',          'Amlodipine 5mg',       'Blood pressure (calcium blocker)','tablet', 220, 30,  4.50,  '2027-05-15'),
('Tritace',          'Ramipril 5mg',         'Blood pressure (ACE inhibitor)',  'tablet', 200, 30,  5.10,  '2027-10-01'),
('Aspavor',          'Atorvastatin 10mg',    'Cholesterol management (low dose)','tablet',160, 20, 4.20,  '2027-08-12'),
('Lexamil',          'Escitalopram 10mg',    'Antidepressant',                  'tablet', 130, 20,  6.80,  '2027-04-05'),
('Lyrica',           'Pregabalin 75mg',      'Nerve pain',                      'capsule', 95, 15, 12.30,  '2027-02-28'),
('Adco-Loperamide',  'Loperamide 2mg',       'Anti-diarrheal',                  'capsule',180, 30,  1.80,  '2027-06-10'),
('Allergex',         'Chlorpheniramine 4mg', 'Antihistamine',                   'tablet', 240, 40,  1.50,  '2027-09-25');


INSERT INTO appointments (patient_id, doctor_id, appointment_date, duration_minutes, reason, status, booked_by_user_id, consultation_notes) VALUES
(1, 1, '2026-04-10 09:00:00', 30, 'Annual check-up',           'completed', 5, 'Healthy, no concerns. BP 120/78.'),
(2, 1, '2026-04-15 10:30:00', 30, 'BP follow-up',              'completed', 5, 'BP stable. Continue lisinopril.'),
(3, 2, '2026-04-20 09:00:00', 45, 'Skin rash consultation',    'completed', 6, 'Allergic dermatitis. Topical cream prescribed.'),
(6, 3, '2026-05-02 14:00:00', 60, 'Heart consultation',        'completed', 5, 'Post-bypass review. All clear. Continue meds.'),
(8, 1, '2026-05-05 11:00:00', 30, 'Cholesterol review',        'completed', 5, 'LDL down to 110. Continue Lipitor.'),
(4, 1, '2026-05-15 10:00:00', 30, 'Diabetes follow-up',        'scheduled', 5, NULL),
(7, 2, '2026-05-15 14:00:00', 45, 'Pregnancy check-up',        'scheduled', 6, NULL),
(10, 2, '2026-05-15 15:30:00', 30, 'Child wellness visit',     'scheduled', 5, NULL),
(5, 1, '2026-05-18 09:00:00', 30, 'Allergy assessment',        'scheduled', 6, NULL),
(9, 1, '2026-05-19 11:30:00', 30, 'Migraine consultation',     'scheduled', 5, NULL),
(2, 1, '2026-05-20 10:00:00', 30, 'Quarterly BP check',        'scheduled', 5, NULL),
(6, 3, '2026-05-21 14:00:00', 60, 'Cardiac follow-up',         'scheduled', 5, NULL),
(8, 1, '2026-05-22 11:00:00', 30, 'Lipid panel review',        'scheduled', 6, NULL),
(3, 1, '2026-05-12 13:00:00', 30, 'Routine check-up',          'cancelled', 5, NULL);


INSERT INTO prescriptions (appointment_id, patient_id, doctor_id, dispensed_by_user_id, status, diagnosis, notes, issued_at, dispensed_at) VALUES
(2, 2, 1, 5, 'dispensed',   'Stage 1 Hypertension',  'Continue BP monitoring',  '2026-04-15 11:00:00', '2026-04-15 11:30:00'),
(3, 3, 2, 6, 'dispensed',   'Allergic dermatitis',   'Apply twice daily',        '2026-04-20 09:45:00', '2026-04-20 10:00:00'),
(5, 8, 1, 5, 'dispensed',   'Hypercholesterolaemia', 'Continue statin therapy',  '2026-05-05 11:30:00', '2026-05-05 12:00:00'),
(1, 1, 1, NULL, 'pending', 'Annual check, prophylactic', 'Standard refill', '2026-05-15 09:30:00', NULL);


INSERT INTO prescription_items (prescription_id, inventory_id, dosage, frequency, duration_days, quantity, unit_price_at_issue) VALUES
(1, 15, '5mg',  'Once daily',     30, 30, 5.10),
(2, 20, '4mg',  'Twice daily',    7,  14, 1.50),
(2, 12, '1 application', 'Three times daily', 7, 1, 65.00),
(3, 5,  '20mg', 'Once daily evening', 30, 30, 6.50),
(4, 1,  '500mg','As needed for pain', 7, 20, 1.20);


INSERT INTO prescription_status_log (prescription_id, changed_by_user_id, old_status, new_status, comment, changed_at) VALUES
(1, 2, NULL,        'pending',   'Issued by Dr. Smith',           '2026-04-15 11:00:00'),
(1, 5, 'pending',   'dispensed', 'Dispensed by reception',         '2026-04-15 11:30:00'),
(2, 3, NULL,        'pending',   'Issued by Dr. Naidoo',          '2026-04-20 09:45:00'),
(2, 6, 'pending',   'dispensed', 'Dispensed by Thandi',           '2026-04-20 10:00:00'),
(3, 2, NULL,        'pending',   'Issued by Dr. Smith',           '2026-05-05 11:30:00'),
(3, 5, 'pending',   'dispensed', 'Dispensed; patient confirmed',   '2026-05-05 12:00:00'),
(4, 2, NULL,        'pending',   'Issued by Dr. Smith - awaiting dispense','2026-05-15 09:30:00');


INSERT INTO billing (patient_id, appointment_id, invoice_number, total_amount, amount_paid, payment_status, payment_method, billing_date, paid_date, notes) VALUES
(2, 2, 'INV-2026-0001',  603.00,  603.00, 'paid',           'card',        '2026-04-15', '2026-04-15 11:35:00', 'Consultation + Tritace 30'),
(3, 3, 'INV-2026-0002',  736.00,  736.00, 'paid',           'medical_aid', '2026-04-20', '2026-04-20 10:15:00', 'Consultation + Allergex + Voltaren Gel'),
(6, 4, 'INV-2026-0003',  850.00,  850.00, 'paid',           'eft',         '2026-05-02', '2026-05-02 15:10:00', 'Cardiology consultation'),
(8, 5, 'INV-2026-0004',  645.00,  645.00, 'paid',           'card',        '2026-05-05', '2026-05-05 12:05:00', 'Consultation + Lipitor 30'),
(1, 1, 'INV-2026-0005',  450.00,    0.00, 'pending',        NULL,          '2026-04-10', NULL,                  'Awaiting payment'),
(2, 11, 'INV-2026-0006', 450.00,  200.00, 'partially_paid', 'cash',        '2026-05-20', NULL,                  'Partial cash payment received');


INSERT INTO billing_items (billing_id, item_type, description, prescription_item_id, inventory_id, quantity, unit_price, line_total) VALUES
(1, 'consultation', 'GP Consultation - Dr. Smith',   NULL, NULL,  1,  450.00, 450.00),
(1, 'medication',   'Tritace 5mg x 30',              1,    15,    30,   5.10, 153.00),
(2, 'consultation', 'Paediatric Consultation - Dr. Naidoo', NULL, NULL, 1, 650.00, 650.00),
(2, 'medication',   'Allergex 4mg x 14',             2,    20,    14,   1.50,  21.00),
(2, 'medication',   'Voltaren Gel x 1',              3,    12,    1,   65.00,  65.00),
(3, 'consultation', 'Cardiology Consultation - Dr. Nkosi', NULL, NULL, 1, 850.00, 850.00),
(4, 'consultation', 'GP Consultation - Dr. Smith',   NULL, NULL,  1,  450.00, 450.00),
(4, 'medication',   'Lipitor 20mg x 30',             4,    5,     30,   6.50, 195.00),
(5, 'consultation', 'GP Consultation - Dr. Smith',   NULL, NULL,  1,  450.00, 450.00),
(6, 'consultation', 'GP Consultation - Dr. Smith',   NULL, NULL,  1,  450.00, 450.00);


INSERT INTO notifications (user_id, type, title, message, related_table, related_id, is_read) VALUES
(1, 'low_stock',           'Low Stock: Ventolin Inhaler',  'Ventolin Inhaler is below minimum threshold (8 / 10).', 'inventory', 7, 0),
(1, 'low_stock',           'Low Stock: Adco-Zolpidem',     'Adco-Zolpidem is below minimum threshold (12 / 20).',   'inventory', 8, 0),
(1, 'expiry_warning',      'Expiring Soon: Cipro',         'Cipro expires on 2026-06-05 (21 days).',                'inventory', 9, 0),
(1, 'expiry_warning',      'Expiring Soon: Augmentin',     'Augmentin expires on 2026-06-10 (26 days).',            'inventory', 10, 0),
(5, 'prescription_ready',  'Prescription Pending Dispense','New prescription for Jane Patient awaiting dispense.',   'prescriptions', 4, 0),
(2, 'appointment_reminder','Upcoming Appointment',         'Mike Pillay (Diabetes follow-up) at 10:00 today.',      'appointments', 6, 0),
(7, 'appointment_reminder','Appointment Today',            'Your pregnancy check-up with Dr. Naidoo is at 14:00.',  'appointments', 7, 0),
(10,'appointment_reminder','Appointment Today',            'Your child wellness visit with Dr. Naidoo is at 15:30.','appointments', 8, 0);
