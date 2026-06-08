# MedicOM Clinic Management System

Web-based clinic management system built for IT Project 700.

## Setup (XAMPP)

1. Install XAMPP (PHP 8.1+).
2. Extract this project into `htdocs` so the path is `htdocs/medicom`.
3. Copy `config/db.example.php` to `config/db.php`. Adjust credentials if your XAMPP MySQL isn't the default (`root` with empty password).
4. Start Apache + MySQL from the XAMPP Control Panel.
5. Open `http://localhost/phpmyadmin`, create a new database called `medicom_db` with collation `utf8mb4_unicode_ci`.
6. Select `medicom_db`, click Import, choose `database/schema.sql`, click Go.
7. Then import `database/seed.sql` for test data.
8. Visit `http://localhost/medicom` and log in.

## Test Accounts

All passwords are `Password123`.

| Role         | Email                     |
|--------------|---------------------------|
| Admin        | admin@medicom.test        |
| Doctor       | dr.smith@medicom.test     |
| Receptionist | reception@medicom.test    |
| Patient      | patient@medicom.test      |

## Conventions

- File names: lowercase with hyphens
- Database columns: snake_case
- PHP variables/functions: camelCase
- All SQL via PDO prepared statements
- Escape output with `e()` when displaying user data
