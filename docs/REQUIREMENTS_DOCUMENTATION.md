# Requirements Documentation

## 1. Document Purpose

This document defines the functional and non-functional requirements of the **PICTO - Records and Tracking System (PICTO-RTS)**.

It is intended to help:

- management understand what the system is expected to do
- developers understand the scope of the application
- future maintainers verify whether new changes still match the original goals

---

## 2. Project Background

PICTO needs a centralized internal system to monitor office records and workflow. Before the system, many records were tracked through spreadsheets, shared files, and manual routing practices. This made it harder to:

- track the current location of records
- monitor pending tasks
- monitor financial activity
- review history of movement and updates
- generate quick reports
- continue work smoothly when personnel change

The system was built to solve those operational gaps.

---

## 3. Business Goal

The system must provide a single internal platform where PICTO staff can:

- encode records
- route records
- monitor task status
- generate reports
- review activity history
- collaborate through comments, approvals, and notifications

---

## 4. Stakeholders

### Primary Stakeholders

- PICTO management
- Admin users
- Regular office users
- Future developers / maintainers

### Secondary Stakeholders

- Offices receiving routed records
- Personnel needing printed or exported reports

---

## 5. Scope of the System

### Included in Scope

- document monitoring
- financial monitoring
- task / to-do monitoring
- office and user management
- notifications
- approvals
- activity logging
- filtered reports and printable exports
- public tracking page for documents

### Outside Current Scope

- mobile application
- external API integrations beyond current email/reporting behavior
- full workflow engine with multi-stage configurable BPM
- public self-service portal for all modules

---

## 6. Functional Requirements

### 6.1 Authentication and Access Control

The system shall:

- require authenticated access for internal modules
- support user login and profile management
- support at least two roles:
  - admin
  - user
- restrict user management and office management to admin users

### 6.2 Dashboard

The system shall:

- display summary counts for documents, financial records, and tasks
- display current reminders for tasks
- display recent activity
- display approval and workload indicators
- provide a quick overview of operational status

### 6.3 Document Monitoring

The system shall:

- allow encoding of document records
- support incoming and outgoing directions
- support routing and receiving documents
- store subject, particulars, references, dates, and office details
- support file attachments
- support internal, external, and travel-order related document categories
- detect possible duplicate records before save
- support report generation for filtered results

### 6.4 Financial Monitoring

The system shall:

- allow creation of financial records
- store PR, PO, OBR, voucher, supplier, type, and amount data
- support routing and receiving of financial records
- support status and progress updates
- generate a reference code for new records
- detect possible duplicates before save
- support report generation for filtered results

### 6.5 To-Do Monitoring

The system shall:

- allow creation and updating of tasks
- support priorities and statuses
- support deadlines / due dates
- support assignment labels
- support recurring tasks
- support subtasks / checklist entries
- support report generation for filtered results

### 6.6 Notifications

The system shall:

- show in-app notifications for important actions
- support notification categories
- support unread/read state
- support notification preferences per user

### 6.7 Collaboration Features

The system shall:

- allow comments on major record types
- log activity history on major record types
- support approval requests and review
- support pinned records
- support saved filters

### 6.8 Reports and Printing

The system shall:

- allow printing of filtered table data
- allow PDF export of reports
- allow row-based and filtered-result reporting
- show a printable report header and footer aligned with office branding

### 6.9 Public Tracking

The system shall:

- provide a public page to search and track documents without full system login

---

## 7. Non-Functional Requirements

### 7.1 Usability

The system should:

- present a simple office-oriented interface
- support quick filtering and searching
- support easy review of status and routing information

### 7.2 Maintainability

The system should:

- follow Laravel MVC structure
- separate route, controller, model, and view logic
- include developer documentation
- include technical comments in important source files

### 7.3 Performance

The system should:

- return list pages in a reasonable time for normal office usage
- paginate major list views
- use indexes for frequently searched data where applicable

### 7.4 Reliability

The system should:

- preserve historical office records
- avoid accidental duplication of references when possible
- restrict unsafe modification of approved or pending-approval records

### 7.5 Security

The system should:

- restrict admin-only features
- protect authenticated routes
- avoid exposing internal records publicly except through the intended tracking page

---

## 8. Data Requirements

The system requires persistent storage of:

- users
- offices
- documents
- document routes
- document files
- financial records
- financial routes
- financial attachments
- todos
- todo subtasks
- approvals
- comments
- activity logs
- notifications
- notification preferences
- pins
- saved filters

---

## 9. Reporting Requirements

Management requires the system to support:

- quick status review
- printable reports
- PDF reports
- historical tracking
- continuity for future developers

For this reason, system documentation is also a project requirement, not only a convenience.

---

## 10. Handover Requirement

The project must include enough documentation so another developer can continue it after turnover. At minimum, the handover package should include:

- requirements documentation
- architecture design documentation
- technical documentation
- technical documentation embedded in source code

This requirement directly supports continuity when the current developer leaves the project.

