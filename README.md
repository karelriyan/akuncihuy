# MAJU JAYA — Accounting Information System

A PHP + MySQL accounting website for Fotokopi & ATK Maju Jaya.

## Requirements
- XAMPP (Apache + MySQL)
- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Modern browser

## Installation
1. Extract the `maju_jaya_accounting` folder into `C:\xampp\htdocs\`.
2. Open XAMPP and start **Apache** and **MySQL**.
3. Open `http://localhost/phpmyadmin`.
4. Choose **Import**, select `database.sql` from this folder, and import it.
5. If your MySQL username/password is different from the XAMPP default, update `config/database.php`.
6. Open `http://localhost/maju_jaya_accounting/`.

## Included modules
- Dashboard and financial overview
- Chart of Accounts (22 accounts imported from the provided workbook)
- Product, Customer, Supplier, and Employee master data (CRUD)
- Financial Reports: Income Statement, Statement of Financial Position, Trial Balance
- Journal entry creation with debit/credit balance validation
- Business profile settings

## Notes
- The imported COA `saldo_referensi` values are reference values from the workbook. They are not automatically posted as opening journal entries.
- Before any journal is posted, the financial report summary displays the imported reference balances.
- Once journal entries are posted, the reports calculate from journal details.
- This is a local academic/demo application. Before production deployment, add authentication, role-based authorization, CSRF protection, audit logs, backups, and server hardening.
