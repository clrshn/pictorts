# PICTO - Records and Tracking System
# Master Documentation

## 1. Document Purpose

This document is the consolidated documentation for the **PICTO - Records and Tracking System (PICTO-RTS)**.

It combines:

- requirements documentation
- architecture design documentation
- technical documentation
- source-code documentation notes

The purpose of this file is to make project turnover easier. If the current developer leaves the project, another developer should be able to use this document to understand, maintain, and continue the system.

---

## 2. Project Overview

### System Name

**PICTO - Records and Tracking System (PICTO-RTS)**

### Project Background

The system was developed to centralize the monitoring and routing of office records handled by PICTO. Before this system, many records were tracked through spreadsheets, shared folders, and manual office processes. That made continuity, monitoring, and reporting more difficult.

The system now provides one internal platform for:

- records monitoring
- office routing
- task management
- financial tracking
- collaboration and approvals
- report generation

### Main Goal

The main goal is to help PICTO manage and monitor documents, financial activity, and tasks in one system while keeping enough technical documentation so future developers can continue the project after handover.

---

## 3. Requirements Documentation

### 3.1 Functional Requirements

The system shall:

- allow authenticated users to access internal modules
- support admin and user roles
- provide a dashboard with summary monitoring
- allow creation, viewing, updating, and tracking of documents
- allow routing and receiving of documents
- support incoming and outgoing document workflows
- support travel-order related document records
- allow creation, viewing, updating, and tracking of financial records
- allow routing and receiving of financial records
- support status and progress monitoring for financial records
- allow creation and monitoring of to-do tasks
- support due dates, priorities, and task statuses
- support recurring tasks
- support subtasks / checklist items
- support comments on major record types
- support approvals and approval review
- support notifications
- support saved filters and pinned records
- support printable and PDF reports
- provide a public document tracking page

### 3.2 Non-Functional Requirements

The system should:

- be understandable by future developers
- preserve historical records safely
- follow Laravel MVC conventions
- provide clear file/module separation
- be maintainable through documentation and source comments
- support common office workflows without requiring technical knowledge from end users

### 3.3 Handover Requirement

The project must include documentation that helps another developer continue the system after turnover. For that reason, this master document and the embedded source code comments are part of the project deliverables.

---

## 4. Stakeholders

### Primary Stakeholders

- PICTO management
- system administrators
- regular office users
- future developers / maintainers

### Secondary Stakeholders

- offices receiving routed records
- personnel using reports for monitoring and presentation

---

## 5. Scope of the System

### Included Scope

- dashboard monitoring
- document monitoring and routing
- financial monitoring and routing
- to-do monitoring
- approvals, comments, pins, saved filters
- notifications
- office and user management
- report generation and printing
- public document tracking

### Current Out-of-Scope Items

- mobile application
- full external API integration platform
- advanced role-permission matrix beyond current admin/user logic
- public self-service access for all system modules

---

## 6. Architecture Design Documentation

### 6.1 Architecture Style

The application follows a **Laravel MVC architecture**.

Main layers:

- **Routes**
- **Controllers**
- **Models**
- **Services**
- **Views**

### 6.2 High-Level Structure

#### Routing Layer

Main route file:

- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\routes\web.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\routes\web.php)

This file defines:

- public routes
- authenticated routes
- admin-only routes
- module routes
- report routes
- notification routes

#### Controller Layer

Main controllers:

- `DashboardController`
- `DocumentController`
- `FinancialController`
- `TodoController`
- `NotificationController`
- `ApprovalController`
- `CommentController`
- `TableReportController`
- `GlobalSearchController`
- `TrackController`
- `UserController`
- `OfficeController`
- `ProfileController`

#### Model Layer

Main models:

- `User`
- `Office`
- `Document`
- `DocumentRoute`
- `DocumentFile`
- `FinancialRecord`
- `FinancialRoute`
- `FinancialAttachment`
- `Todo`
- `TodoSubtask`
- `Approval`
- `Comment`
- `ActivityLog`
- `NotificationPreference`
- `Pin`
- `SavedFilter`

#### Service Layer

Shared services:

- `ActivityLogService`
- `InAppNotificationService`
- `EmailNotificationService`

#### View Layer

Main view folders:

- `resources/views/documents`
- `resources/views/financial`
- `resources/views/todos`
- `resources/views/users`
- `resources/views/offices`
- `resources/views/notifications`
- `resources/views/profile`
- `resources/views/search`
- `resources/views/exports`

Important single views:

- `dashboard.blade.php`
- `track.blade.php`

---

## 7. System Modules

### 7.1 Dashboard Module

Main files:

- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\DashboardController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\DashboardController.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views\dashboard.blade.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views\dashboard.blade.php)

Purpose:

- provide summary counts
- show trends
- show reminders
- show recent activity
- give management a quick operational overview

### 7.2 Documents Module

Main files:

- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\DocumentController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\DocumentController.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\Document.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\Document.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\DocumentRoute.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\DocumentRoute.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\DocumentFile.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\DocumentFile.php)

Main responsibilities:

- encode records
- route documents
- receive documents
- manage document files
- filter and search documents
- detect possible duplicates
- handle travel-order related document records
- generate printable and PDF reports

### 7.3 Financial Module

Main files:

- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\FinancialController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\FinancialController.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\FinancialRecord.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\FinancialRecord.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\FinancialRoute.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\FinancialRoute.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\FinancialAttachment.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\FinancialAttachment.php)

Main responsibilities:

- encode financial records
- route financial records
- receive financial records
- update financial status and progress
- generate financial reference codes
- detect possible duplicates
- generate printable and PDF reports

### 7.4 To-Do Module

Main files:

- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TodoController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TodoController.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TodoSubtaskController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TodoSubtaskController.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\Todo.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\Todo.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\TodoSubtask.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\TodoSubtask.php)

Main responsibilities:

- create and monitor tasks
- track due dates and priorities
- support recurring tasks
- support subtasks
- support reports and reminders

### 7.5 Collaboration Layer

Shared collaboration features are applied across major modules through:

- comments
- approvals
- activity logs
- pins

Important shared file:

- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\Concerns\HasCollaborationFeatures.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\Concerns\HasCollaborationFeatures.php)

### 7.6 Notifications

Main files:

- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\NotificationController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\NotificationController.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Services\InAppNotificationService.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Services\InAppNotificationService.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Services\EmailNotificationService.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Services\EmailNotificationService.php)

### 7.7 Reports and Printing

Main files:

- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TableReportController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TableReportController.php)
- `App\Support\TableExport`

Purpose:

- preview filtered reports
- print reports
- generate PDF output

---

## 8. Technical Documentation

### 8.1 Technology Stack

- PHP `^8.2`
- Laravel `^12.0`
- Blade templates
- Vite
- Tailwind CSS
- AlpineJS
- DOMPDF

### 8.2 Main Database Source

The schema is mainly defined through migrations in:

- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\database\migrations](C:\Users\Clarisahaina\xampp\htdocs\pictorts\database\migrations)

Reference SQL files also exist:

- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\pictorts.sql](C:\Users\Clarisahaina\xampp\htdocs\pictorts\pictorts.sql)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\offices.sql](C:\Users\Clarisahaina\xampp\htdocs\pictorts\offices.sql)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\todos.sql](C:\Users\Clarisahaina\xampp\htdocs\pictorts\todos.sql)

### 8.3 Important Tables

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
- `approvals`
- `comments`
- `activity_logs`
- `notifications`
- `notification_preferences`
- `pins`
- `saved_filters`

### 8.4 Important Technical Behaviors

#### Approval Locking

Documents, financial records, and tasks enforce approval locking logic so regular users cannot freely change records that are:

- pending approval
- already approved

#### Duplicate Detection

Documents and financial records use warning-based duplicate detection instead of strict hard blocking, because office records may contain near-duplicate historical entries that still need human review.

#### Report Generation

Filtered table data can be turned into:

- CSV
- print preview
- PDF

#### Recurring Task Logic

To-dos support recurring task fields so repeated tasks can continue automatically as part of office workflow.

---

## 9. Source Code Documentation Embedded in Code

Technical documentation is also embedded in important source files through comments.

Key files updated with explanatory comments:

- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\routes\web.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\routes\web.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\Concerns\HasCollaborationFeatures.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models\Concerns\HasCollaborationFeatures.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\DashboardController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\DashboardController.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\DocumentController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\DocumentController.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\FinancialController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\FinancialController.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TodoController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TodoController.php)
- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TableReportController.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers\TableReportController.php)

The purpose of those comments is to explain:

- why certain business rules exist
- why approval locks exist
- why duplicate detection is warning-based
- why report data is cached
- why dashboard queries are structured as summaries

---

## 10. Literate Programming and Elucidative Programming

### 10.1 Literate Programming

Literate programming treats software not only as code to execute, but also as a system that should be understandable in written form.

In this project, that idea is applied in a practical way by combining:

- this master documentation
- module-level technical documentation
- explanatory code comments in important files

The goal is to help the next developer understand both:

- what the code does
- why it was written that way

### 10.2 Elucidative Programming

Elucidative programming means the code and documentation should clarify the logic instead of leaving business rules hidden.

This project follows that approach by explicitly explaining:

- approval lock behavior
- duplicate warning behavior
- report caching behavior
- recurring task behavior
- shared collaboration behavior

### 10.3 Why This Matters for Handover

Without explanatory programming practices, another developer may understand Laravel syntax but still misunderstand the office workflow rules. These sections and comments reduce that risk and improve project continuity.

---

## 11. User Documentation

Besides developer-oriented documentation, the project also needs user-oriented explanation.

User documentation focuses on:

- how staff use the system
- how modules behave from a user point of view
- how to complete common tasks

User documentation is different from technical documentation because it is written for end users instead of developers.

The starter user documentation file is:

- [C:\Users\Clarisahaina\xampp\htdocs\pictorts\docs\USER_DOCUMENTATION.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\docs\USER_DOCUMENTATION.md)

### 11.1 Minimum Topics for User Documentation

The user documentation should cover:

- logging in
- using the dashboard
- creating and routing documents
- creating and routing financial records
- managing tasks
- checking notifications
- printing reports

---

## 12. Composing User Documentation

When composing user documentation for this system, the content should be written in simple, task-based language.

### 12.1 Recommended Writing Style

The user guide should:

- use plain language
- avoid technical programming terms
- provide step-by-step instructions
- explain what each page is for
- describe common mistakes and corrections

### 12.2 Recommended Structure for a Full User Manual

1. Introduction
2. Logging In
3. Dashboard Overview
4. Documents Module
5. Financial Module
6. To-Do Module
7. Notifications
8. Reports and Printing
9. Profile Settings
10. Troubleshooting

### 12.3 Recommended Presentation Style

For a future formal user manual, it is recommended to include:

- screenshots
- page labels
- button descriptions
- examples of common workflows

---

## 13. Local Setup and Continuation

Typical setup commands:

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```

The project is currently used in a Windows/XAMPP-friendly environment, but can be adapted to another Laravel-compatible environment.

---

## 14. Important Continuation Notes for Next Developers

### Read These Files First

1. [C:\Users\Clarisahaina\xampp\htdocs\pictorts\docs\PICTO_RTS_MASTER_DOCUMENTATION.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\docs\PICTO_RTS_MASTER_DOCUMENTATION.md)
2. [C:\Users\Clarisahaina\xampp\htdocs\pictorts\README.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\README.md)
3. [C:\Users\Clarisahaina\xampp\htdocs\pictorts\routes\web.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\routes\web.php)
4. [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models)
5. [C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers)
6. [C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views)

### Change Carefully

Future developers should be careful when editing:

- document numbering logic
- financial reference code logic
- report export logic
- approval locking
- imported historical records
- notification behavior

### Verify After Major Changes

After major updates, test:

1. dashboard
2. documents module
3. financial module
4. todos module
5. approvals
6. notifications
7. print/PDF reports

---

## 15. Technical Handover Summary

This document serves as the official single-file technical handoff package for the system. It is intended to help the next developer understand:

- what the system is supposed to do
- how it is structured
- how it is technically implemented
- where to continue development
- what business rules must be preserved

---

## 16. Suggested Statement for Management

If management asks what this file is, you can say:

> This is the consolidated documentation for the PICTO Records and Tracking System. It contains the system requirements, architecture design, technical implementation notes, and references to the embedded code documentation so another developer can continue the project after turnover.
