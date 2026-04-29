# Architecture Design Documentation

## 1. Document Purpose

This document explains the system architecture of **PICTO - Records Monitoring System (PICTO-RMS)** so future developers can understand how the application is structured and where responsibility is divided.

---

## 2. Architecture Style

The system follows a **Laravel MVC web application architecture**.

### Main Layers

- **Routing layer**
  Handles URL mapping and middleware rules.
- **Controller layer**
  Handles request validation, workflow logic, filtering, export behavior, and orchestration.
- **Model layer**
  Represents database tables and relationships using Eloquent.
- **Service layer**
  Handles reusable processes such as notifications and activity logs.
- **View layer**
  Renders UI with Blade templates.

---

## 3. High-Level Component Design

### 3.1 Entry Points

- `routes/web.php`
- authentication routes from `auth.php`

### 3.2 Major Controllers

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

### 3.3 Major Models

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

### 3.4 Supporting Services

- `ActivityLogService`
- `InAppNotificationService`
- `EmailNotificationService`

---

## 4. Module Architecture

### 4.1 Dashboard Module

**Purpose**

Provides a consolidated operational overview.

**Inputs**

- documents table
- financial records table
- todos table
- approvals table
- activity logs table

**Outputs**

- summary cards
- charts
- reminder panels
- recent activity

### 4.2 Documents Module

**Purpose**

Handles document encoding, routing, receiving, file attachments, and reporting.

**Core Entities**

- `Document`
- `DocumentRoute`
- `DocumentFile`

**Important Design Notes**

- document direction distinguishes incoming/outgoing flow
- delivery scope distinguishes internal/external use cases
- travel-order related records are handled within the document module
- duplicate detection is handled at controller level before save

### 4.3 Financial Module

**Purpose**

Tracks financial records and their movement.

**Core Entities**

- `FinancialRecord`
- `FinancialRoute`
- `FinancialAttachment`

**Important Design Notes**

- financial status and progress are separate concepts
- `reference_code` is generated automatically
- duplicate detection is handled before save

### 4.4 To-Do Module

**Purpose**

Tracks office tasks and deadlines.

**Core Entities**

- `Todo`
- `TodoSubtask`

**Important Design Notes**

- recurring logic is part of the task lifecycle
- subtasks act as a lightweight checklist
- approval lock rules also apply here

### 4.5 Collaboration Layer

**Purpose**

Adds cross-cutting collaboration features to major modules.

**Shared Capabilities**

- comments
- activity logs
- approvals
- pins

**Shared Mechanism**

The `HasCollaborationFeatures` trait enables polymorphic relationships so multiple modules can share one collaboration design.

### 4.6 Reporting Layer

**Purpose**

Transforms filtered UI data into printable and exportable reports.

**Core Parts**

- `TableReportController`
- `App\Support\TableExport`
- export views

---

## 5. Route and Access Design

### Access Structure

- public root redirect
- public track search
- authenticated + verified application routes
- admin-only routes for users and offices

### Security Pattern

- auth middleware protects internal pages
- admin middleware protects management features
- approval lock logic prevents unsafe record changes during or after approval

---

## 6. Data Flow Overview

### Document Flow

1. user creates document
2. document is stored
3. routes may be added
4. receiving office/user updates current status/location
5. notifications and logs may be recorded
6. report/export may be generated

### Financial Flow

1. user creates financial record
2. reference code is generated
3. routing/receiving updates movement
4. status or progress changes are recorded
5. notifications and logs may be recorded
6. report/export may be generated

### To-Do Flow

1. user creates task
2. due date / priority / assignment are tracked
3. subtasks may be added
4. recurrence may generate future tasks
5. comments, logs, approvals, and notifications may be triggered

---

## 7. UI Architecture

The UI is Blade-based and organized around module views.

### Main UI Areas

- dashboard
- documents views
- financial views
- todos views
- users views
- offices views
- notifications views
- profile views
- report preview/export views

### Important UI Design Note

A meaningful amount of styling is embedded directly inside Blade templates, especially for:

- dashboard
- reporting/printing
- form-specific layout adjustments

This means future UI refactoring should be done carefully, because visual behavior is not fully centralized in one stylesheet.

---

## 8. Database Architecture Summary

The database is relational and centered on three main operational domains:

- documents
- financial records
- todos

Cross-cutting supporting domains:

- users and offices
- approvals
- activity logs
- comments
- notifications
- personal preferences

This design makes the system modular while still allowing shared collaboration features.

---

## 9. Architectural Strengths

- standard Laravel structure
- clear module separation
- reusable collaboration trait
- report generation built as a dedicated concern
- import scripts separated from request controllers

---

## 10. Architectural Risks / Future Refactoring Opportunities

- some controller classes are becoming large and may later benefit from service extraction
- inline Blade styling may become harder to maintain over time
- report customization logic may continue growing and could later move into a dedicated reporting service layer
- permission logic may eventually need to evolve from simple role-based access to office-based granular permissions

---

## 11. Recommended Continuation Strategy

If another developer continues this system, recommended order of understanding is:

1. routes
2. models and relationships
3. dashboard controller
4. documents, financial, and todo controllers
5. collaboration controllers and services
6. export/report logic
7. UI views

