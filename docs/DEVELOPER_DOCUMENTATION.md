# Developer Documentation

## 1. Purpose of This Document

This document is the **developer handoff / development documentation** for the **PICTO - Records and Tracking System**.

Its purpose is to help the next developer:

- understand what the system does
- set up the project locally
- understand the database and major modules
- know where important logic lives
- continue development safely
- troubleshoot common issues

This is different from a **user manual**.

- A **user manual** explains how end users operate the system.
- A **developer document** explains how the system is built and how another programmer can maintain or extend it.

For the current handoff, this developer document is the more important deliverable because it helps continuity when the project is turned over to another developer.

---

## 2. System Overview

### System Name

**PICTO - Records and Tracking System (PICTO-RTS)**

### Main Goal

The system is used by PICTO to centralize monitoring and routing of:

- documents
- financial records
- tasks / to-do items
- approvals and collaboration activity
- notifications and reports

### Main Users

- Admin users
- Regular office users

### Main Features

- Dashboard with summary analytics
- Document encoding and routing
- Financial monitoring and routing
- To-do / task tracking
- Office management
- User management
- Notifications
- Comments and activity logs
- Approval workflow
- Saved filters and pinned records
- Table report preview / print / PDF generation
- Public document tracking page

---

## 3. Technology Stack

### Backend

- PHP `^8.2`
- Laravel `^12.0`

### Frontend

- Blade templates
- Vite
- Tailwind CSS
- custom CSS inside Blade views
- AlpineJS is available in dependencies

### Reporting

- `barryvdh/laravel-dompdf` for PDF generation

### Development / Testing Tools

- Laravel Breeze
- PHPUnit
- Laravel Pint
- Laravel Pail

---

## 4. Environment and Dependencies

### Composer Packages

Important packages from [composer.json](C:\Users\Clarisahaina\xampp\htdocs\pictorts\composer.json):

- `laravel/framework`
- `barryvdh/laravel-dompdf`
- `laravel/tinker`

### NPM Packages

Important packages from [package.json](C:\Users\Clarisahaina\xampp\htdocs\pictorts\package.json):

- `vite`
- `laravel-vite-plugin`
- `tailwindcss`
- `axios`
- `alpinejs`

### Typical Local Setup

This project appears to be used in a **XAMPP / local Windows environment**, but can also run in other Laravel-compatible setups.

Recommended local requirements:

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL / MariaDB
- XAMPP or an equivalent local server stack

---

## 5. How to Run the Project Locally

### Initial Setup

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```

### If using XAMPP

If this project is hosted through `htdocs` in XAMPP:

- place the project in `xampp/htdocs`
- configure the `.env` database connection to match your MySQL/MariaDB instance
- ensure Apache and MySQL are running

### Build for Production

```powershell
npm run build
php artisan optimize
```

---

## 6. Database Notes

### Main Database Source

The database schema is primarily defined through **Laravel migrations** in:

- [database/migrations](C:\Users\Clarisahaina\xampp\htdocs\pictorts\database\migrations)

There are also SQL dump/reference files in the repository:

- [pictorts.sql](C:\Users\Clarisahaina\xampp\htdocs\pictorts\pictorts.sql)
- [offices.sql](C:\Users\Clarisahaina\xampp\htdocs\pictorts\offices.sql)
- [todos.sql](C:\Users\Clarisahaina\xampp\htdocs\pictorts\todos.sql)
- [financial.sql](C:\Users\Clarisahaina\xampp\htdocs\pictorts\financial.sql)

### Important Tables

Core tables based on migrations and models:

- `users`
- `offices`
- `documents`
- `document_routes`
- `document_files`
- `financial_records`
- `financial_routes`
- `financial_attachments`
- `todos`
- `todo_subtasks`
- `comments`
- `activity_logs`
- `approvals`
- `notifications`
- `notification_preferences`
- `pins`
- `saved_filters`
- `sessions`
- `cache`

### Important Schema Evolution

The migration history shows that the system evolved over time and includes:

- renaming `ictu_number` to `picto_number`
- travel order fields for documents
- status normalization from `COMPLETED` to `DONE` for documents
- collaboration features:
  - notifications
  - comments
  - activity logs
  - approvals
  - notification preferences
  - recurring todos
  - subtasks
  - pins
  - saved filters
- financial `reference_code`

### Seeders

Available seeders:

- [database/seeders/DatabaseSeeder.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\database\seeders\DatabaseSeeder.php)
- [database/seeders/UsersSeeder.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\database\seeders\UsersSeeder.php)
- [database/seeders/OfficesSeeder.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\database\seeders\OfficesSeeder.php)
- [database/seeders/FinancialRecordsSeeder.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\database\seeders\FinancialRecordsSeeder.php)
- [database/seeders/InternalDocumentsSeeder.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\database\seeders\InternalDocumentsSeeder.php)

Use with care in an existing database.

---

## 7. High-Level Architecture

This is a standard Laravel MVC-style application:

- `routes/web.php` defines routes
- Controllers handle requests and business logic
- Models define database relationships
- Blade views render the UI
- Services contain reusable logic such as notifications and activity logging

### Request Flow

Typical request flow:

1. user opens route
2. route points to controller action
3. controller validates request and queries models
4. controller returns Blade view or JSON response
5. Blade renders UI

### Shared Collaboration Logic

A reusable trait is used for collaboration-related features:

- [app/Models/Concerns/HasCollaborationFeatures.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\Concerns\HasCollaborationFeatures.php)

This trait adds:

- pins
- comments
- activity logs
- approval

It is currently used by:

- `Document`
- `FinancialRecord`
- `Todo`

---

## 8. Core Modules

### 8.1 Dashboard

Main controller:

- [app/Http/Controllers/DashboardController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\DashboardController.php)

Main view:

- [resources/views/dashboard.blade.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views\dashboard.blade.php)

Dashboard includes:

- document counts
- financial counts
- to-do reminders
- pending approvals
- monthly chart
- recent activity

### 8.2 Documents Module

Main files:

- [app/Http/Controllers/DocumentController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\DocumentController.php)
- [app/Models/Document.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\Document.php)
- [app/Models/DocumentRoute.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\DocumentRoute.php)
- [app/Models/DocumentFile.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\DocumentFile.php)
- [resources/views/documents](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views\documents)

Capabilities:

- encode incoming/outgoing documents
- route documents between offices
- receive documents
- upload document files
- handle travel-order related document records
- detect possible duplicates before saving
- print / export reports

Important fields in `Document`:

- `dts_number`
- `picto_number`
- `document_type`
- `direction`
- `delivery_scope`
- `travel_order_type`
- `subject`
- `particulars`
- `originating_office`
- `to_office`
- `current_office`
- `current_holder`
- `status`

### 8.3 Financial Module

Main files:

- [app/Http/Controllers/FinancialController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\FinancialController.php)
- [app/Models/FinancialRecord.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\FinancialRecord.php)
- [app/Models/FinancialRoute.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\FinancialRoute.php)
- [app/Models/FinancialAttachment.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\FinancialAttachment.php)
- [resources/views/financial](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views\financial)

Capabilities:

- create and manage financial monitoring records
- route financial records
- receive financial records
- update financial status
- attach supporting files
- detect duplicates
- generate `reference_code` automatically
- filter, sort, print, and export reports

Important fields in `FinancialRecord`:

- `type`
- `description`
- `reference_code`
- `supplier`
- `pr_number`
- `pr_amount`
- `po_number`
- `po_amount`
- `obr_number`
- `voucher_number`
- `office_origin`
- `status`
- `progress`

### 8.4 To-Do Module

Main files:

- [app/Http/Controllers/TodoController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TodoController.php)
- [app/Http/Controllers/TodoSubtaskController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TodoSubtaskController.php)
- [app/Models/Todo.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\Todo.php)
- [app/Models/TodoSubtask.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\TodoSubtask.php)
- [resources/views/todos](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views\todos)

Capabilities:

- task creation and monitoring
- due dates
- priority and status updates
- recurring tasks
- subtasks / checklist
- comments, pins, approvals, activity history

### 8.5 User and Office Management

Main files:

- [app/Http/Controllers/UserController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\UserController.php)
- [app/Http/Controllers/OfficeController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\OfficeController.php)
- [app/Models/User.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\User.php)
- [app/Models/Office.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\Office.php)

Notes:

- office and user management routes are admin-only
- users have roles:
  - `admin`
  - `user`

### 8.6 Notifications

Main files:

- [app/Http/Controllers/NotificationController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\NotificationController.php)
- [app/Services/InAppNotificationService.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Services\InAppNotificationService.php)
- [app/Services/EmailNotificationService.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Services\EmailNotificationService.php)
- [resources/views/notifications](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views\notifications)

Capabilities:

- in-app notifications
- unread/read state
- notification feed
- category preferences
- document / financial / task / approval notifications

### 8.7 Comments, Approvals, Activity Logs, Pins, Saved Filters

Main controllers:

- [app/Http/Controllers/CommentController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\CommentController.php)
- [app/Http/Controllers/ApprovalController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\ApprovalController.php)
- [app/Http/Controllers/PinController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\PinController.php)
- [app/Http/Controllers/SavedFilterController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\SavedFilterController.php)

Services:

- [app/Services/ActivityLogService.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Services\ActivityLogService.php)

These features are shared across major records to support collaboration and continuity.

### 8.8 Search and Reporting

Main files:

- [app/Http/Controllers/GlobalSearchController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\GlobalSearchController.php)
- [app/Http/Controllers/TableReportController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TableReportController.php)
- [resources/views/search](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views\search)
- [resources/views/exports](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views\exports)

Reporting supports:

- print preview
- PDF generation
- filtered tabular reports
- selected-row style reports

### 8.9 Public Tracking

Main files:

- [app/Http/Controllers/TrackController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TrackController.php)
- [resources/views/track.blade.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views\track.blade.php)

This is the public-facing track page for document search without requiring login.

---

## 9. Routes Summary

Main route file:

- [routes/web.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\routes\web.php)

High-level route groups:

- `/dashboard`
- `/track-document`
- `/documents`
- `/financial`
- `/todos`
- `/users`
- `/offices`
- `/notifications`
- `/comments`
- `/approvals`
- `/pins`
- `/saved-filters`
- `/profile`

Admin-only routes are wrapped in:

- `Route::middleware('admin')`

Authenticated routes are wrapped in:

- `Route::middleware(['auth', 'verified'])`

---

## 10. Important Models and Relationships

### User

Key relationships:

- belongs to `Office`
- has one `NotificationPreference`
- has many `Comment`
- has many `ActivityLog`

### Document

Key relationships:

- belongs to originating office
- belongs to destination office
- belongs to current office
- belongs to current holder (`User`)
- belongs to encoder (`User`)
- has many routes
- has many files

### FinancialRecord

Key relationships:

- belongs to origin office
- belongs to current office
- belongs to current holder
- belongs to creator
- has many routes
- has many attachments

### Todo

Key relationships:

- belongs to user
- has many subtasks
- supports recurring parent/children

### Shared Collaboration Models

- `Approval`
- `Comment`
- `ActivityLog`
- `Pin`
- `SavedFilter`

---

## 11. File / Folder Guide for the Next Developer

### Start Here First

If a new developer will continue the project, the best starting order is:

1. [README.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\README.md)
2. [routes/web.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\routes\web.php)
3. [app/Models](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models)
4. [app/Http/Controllers](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers)
5. [resources/views](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views)
6. [database/migrations](C:\Users\Clarisahaina\xampp\htdocs\pictorts\database\migrations)

### Where to Edit Specific Features

- dashboard UI -> `resources/views/dashboard.blade.php`
- document business logic -> `DocumentController.php`
- financial business logic -> `FinancialController.php`
- task business logic -> `TodoController.php`
- approval logic -> `ApprovalController.php`
- comments -> `CommentController.php`
- notifications -> `NotificationController.php` and notification services
- print/PDF reports -> `TableReportController.php` and `App\Support\TableExport`

---

## 12. Data Import / Special Scripts

Helper scripts are stored in:

- [scripts](C:\Users\Clarisahaina\xampp\htdocs\pictorts\scripts)

Examples:

- `import_documents_so_2024.php`
- `import_documents_so_2024_2026_batch.php`
- `import_indoc_batch.php`
- `import_sp_osp_batch.php`
- `sync_eo_reference_numbers.php`

These appear to support historical data import and cleanup.

Use caution before running them in production:

- back up the database first
- inspect script logic first
- confirm office/code mapping
- test on a copy of the database if possible

---

## 13. Reporting / Printing

The system includes printable and PDF table reports.

Important files:

- [app/Http/Controllers/TableReportController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TableReportController.php)
- `App\Support\TableExport`
- views in [resources/views/exports](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views\exports)

Capabilities:

- filtered report generation
- selected-row report generation
- PDF export
- print preview with PICTO/PGLU style header and footer

---

## 14. Notifications and Email

There are both in-app and email notification features.

See:

- [EMAIL_SETUP.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\EMAIL_SETUP.md)
- `NotificationController`
- `InAppNotificationService`
- `EmailNotificationService`

Mail settings are configured through `.env`.

Test routes also exist for email testing.

---

## 15. Security and Access Control

Current access control is primarily role-based:

- admin
- user

Observed controls include:

- auth and verified middleware
- admin-only route group
- approval locking behavior in Documents and Financial modules

Important note for future developers:

If more granular office-based permissions are needed later, they should be added carefully without breaking existing admin/user assumptions.

---

## 16. Known Continuation Notes

These are important for the next developer to understand:

### 1. The project has evolved quickly

There are many recent features already added, including:

- dashboard redesign
- notifications
- comments
- approvals
- pins
- saved filters
- subtasks
- recurring todos
- print/PDF reporting

Future developers should read recent migration files and controllers carefully before refactoring.

### 2. UI customization is heavily Blade-based

A lot of interface styling is directly inside Blade files, especially dashboards and reports. If the next developer prefers a cleaner frontend structure, they may later refactor styles into dedicated CSS files, but they should do so carefully to avoid visual regressions.

### 3. Some environment assumptions are local/XAMPP-based

The repository contains XAMPP-friendly files and SQL dumps. Deployment to another environment may require:

- `.env` adjustment
- storage link creation
- mail configuration review
- database import/migration review

### 4. Historical imported data matters

The project includes many records imported from existing office files. Future developers must avoid destructive changes to document numbering, office mapping, and historical status values unless clearly approved.

---

## 17. Recommended Handover Checklist

Before turning over this system, the current developer should ideally provide:

- this developer documentation
- updated `.env.example`
- latest database backup
- latest SQL dump if needed
- list of admin accounts
- list of important office code mappings
- explanation of report generation flow
- explanation of notification flow
- explanation of import scripts

Recommended technical handover steps:

1. confirm local environment works
2. confirm database backup exists
3. confirm migrations are up to date
4. confirm admin login works
5. confirm dashboard, documents, financial, and todo modules open correctly
6. confirm print and PDF export work
7. confirm email configuration if used

---

## 18. Suggested Future Documentation

If time permits later, the following can still be added:

- user manual for end users
- admin manual
- deployment guide
- backup and restore guide
- API or route reference
- change request / maintenance log

For now, this developer documentation is the correct and most useful handoff document for continuity.

---

## 19. Short Explanation for Management

If management asks what this document is:

> This is the technical handoff document for the PICTO Records and Tracking System. It explains how the system is structured, how it is run, what modules it contains, and what the next developer needs to know in order to continue development and maintenance.

---

## 20. Maintainer Notes

When continuing development, prioritize these files:

- [routes/web.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\routes\web.php)
- [app/Http/Controllers](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers)
- [app/Models](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models)
- [resources/views](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views)
- [database/migrations](C:\Users\Clarisahaina\xampp\htdocs\pictorts\database\migrations)

When making major changes:

1. back up the database first
2. check migration history
3. test reports
4. test routing actions
5. test approval locks
6. test notifications

