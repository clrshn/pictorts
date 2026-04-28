# Technical Documentation

## 1. Document Purpose

This document explains the technical implementation details of **PICTO - Records and Tracking System (PICTO-RTS)**.

It is intended for:

- developers
- maintainers
- technical reviewers
- system turnover/handover

This document should be read together with:

- [REQUIREMENTS_DOCUMENTATION.md](./REQUIREMENTS_DOCUMENTATION.md)
- [ARCHITECTURE_DESIGN_DOCUMENTATION.md](./ARCHITECTURE_DESIGN_DOCUMENTATION.md)
- [DEVELOPER_DOCUMENTATION.md](./DEVELOPER_DOCUMENTATION.md)

---

## 2. Repository Structure

Top-level important folders/files:

- `app/` - application code
- `bootstrap/` - Laravel bootstrap files
- `config/` - Laravel configuration
- `database/` - migrations and seeders
- `public/` - public assets
- `resources/views/` - Blade UI views
- `routes/` - route definitions
- `scripts/` - import and helper scripts
- `tests/` - automated tests
- `README.md` - project entry documentation

---

## 3. Main Controllers

### DashboardController

Responsibilities:

- collect summary counts
- build chart data
- fetch task reminders
- fetch recent activity

### DocumentController

Responsibilities:

- list/filter documents
- create/update/delete documents
- route and receive documents
- handle duplicate detection
- support travel-order-specific handling
- support CSV / print / PDF output

### FinancialController

Responsibilities:

- list/filter financial records
- create/update/delete financial records
- route and receive financial records
- generate reference codes
- detect duplicates
- support CSV / print / PDF output

### TodoController

Responsibilities:

- list/filter tasks
- create/update/delete tasks
- update task status and priority
- support recurring tasks
- support reporting

### TableReportController

Responsibilities:

- store report payload in cache
- generate preview URL
- generate PDF URL
- serve print or PDF output

### NotificationController

Responsibilities:

- notification feed
- mark read / mark all read
- test notification routes
- notification sending endpoints

---

## 4. Main Models

### Document

Represents tracked document records.

Key technical points:

- uses `HasCollaborationFeatures`
- has office/user relations
- has route/file relations

### FinancialRecord

Represents tracked financial records.

Key technical points:

- uses `HasCollaborationFeatures`
- includes generated `reference_code`
- has route/attachment relations

### Todo

Represents tracked task items.

Key technical points:

- uses `HasCollaborationFeatures`
- supports recurring parent-child relationship
- supports subtasks
- provides presentation helpers such as badge/color accessors

### User

Represents authenticated system user.

Key technical points:

- supports role checking
- supports profile photo
- supports notification preference lookup

---

## 5. Shared Technical Mechanisms

### 5.1 Collaboration Trait

File:

- `app/Models/Concerns/HasCollaborationFeatures.php`

Purpose:

- centralizes morph relationships for comments, logs, approvals, and pins

Benefit:

- keeps collaboration support consistent across records

### 5.2 Activity Logging

File:

- `app/Services/ActivityLogService.php`

Purpose:

- write structured activity entries when important system actions occur

### 5.3 Notifications

Files:

- `app/Services/InAppNotificationService.php`
- `app/Services/EmailNotificationService.php`

Purpose:

- handle user-facing notification events
- separate notification logic from controller-only flow

---

## 6. Reporting and Export Technical Design

Reporting is implemented through:

- controller-side row/header preparation
- support utilities in `App\Support\TableExport`
- preview rendering
- PDF generation through DOMPDF

### Supported Output Modes

- CSV
- print preview
- PDF

### Input Pattern

Controllers typically:

1. filter query
2. build normalized row arrays
3. limit visible columns
4. pass output to `TableExport`

---

## 7. Notification and Approval Lock Logic

Both documents and financial records contain protective workflow rules.

### Pending Approval Lock

Regular users cannot continue modifying a record when:

- an approval exists
- status is `pending`

### Approved Record Lock

Regular users cannot freely modify certain records after approval.

This preserves integrity and supports office review workflows.

---

## 8. Duplicate Detection Logic

Duplicate detection is currently handled at controller level rather than database unique constraints alone.

### Document Duplicate Checks

May compare:

- subject
- particulars
- memorandum number
- OPG reference
- OPA reference
- DTS number
- shared drive link

### Financial Duplicate Checks

May compare:

- description
- supplier
- PR number
- PO number
- OBR number
- voucher number

Reason:

Office records may have near-duplicate operational entries that require warning rather than automatic hard rejection.

---

## 9. Data Import and Migration Support

The project includes helper scripts for historical import:

- `scripts/import_documents_so_2024.php`
- `scripts/import_documents_so_2024_2026_batch.php`
- `scripts/import_indoc_batch.php`
- `scripts/import_sp_osp_batch.php`
- `scripts/sync_eo_reference_numbers.php`

These scripts should be reviewed before use on live data.

Recommended precautions:

1. back up database first
2. test against a copy of production data
3. confirm office and status mapping

---

## 10. Source Code Documentation Strategy

Technical documentation should not exist only in external files.

For continuity, source code should also contain:

- short comments for non-obvious business rules
- comments before multi-step query/report logic
- comments before approval lock or duplicate detection logic
- comments for generated reference/numbering logic

This project now follows that approach in selected key files.

---

## 11. UI and View Structure

The view structure is module-based:

- `resources/views/documents`
- `resources/views/financial`
- `resources/views/todos`
- `resources/views/users`
- `resources/views/offices`
- `resources/views/notifications`
- `resources/views/profile`
- `resources/views/search`
- `resources/views/exports`

Special view files:

- `resources/views/dashboard.blade.php`
- `resources/views/track.blade.php`

### Technical UI Note

Some UI styles are deeply tied to specific Blade templates. This should be considered when making major UI changes because visual logic is not fully abstracted into a component library.

---

## 12. Maintenance Notes for Future Developers

### Change Carefully

Be careful when changing:

- document numbering fields
- financial reference code generation
- travel order document handling
- approval lock behavior
- report export formatting
- notification flow

### Verify After Major Changes

After large modifications, always test:

1. dashboard loads
2. documents encode/route/receive correctly
3. financial records encode/route/receive correctly
4. to-do updates and reminders work
5. approvals lock correctly
6. notifications appear
7. report preview, print, and PDF still work

---

## 13. Suggested Continuation Work

Future developers may still improve:

- centralized CSS structure
- stronger policy-based authorization
- more automated tests
- deployment documentation
- backup/restore documentation
- user manual for non-technical staff

