# PICTO - Records and Tracking System

PICTO - Records and Tracking System (`PICTO-RTS`) is a Laravel-based internal web application for monitoring:

- documents and routing
- financial records and routing
- to-do / task monitoring
- approvals, comments, pins, and activity history
- notifications and printable reports

This repository now uses a project-specific README so the next developer can quickly understand the system instead of starting from the default Laravel documentation.

## Main Documentation

- Master documentation: [docs/PICTO_RTS_MASTER_DOCUMENTATION.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\docs\PICTO_RTS_MASTER_DOCUMENTATION.md)
- User documentation: [docs/USER_DOCUMENTATION.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\docs\USER_DOCUMENTATION.md)
- Requirements documentation: [docs/REQUIREMENTS_DOCUMENTATION.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\docs\REQUIREMENTS_DOCUMENTATION.md)
- Architecture design documentation: [docs/ARCHITECTURE_DESIGN_DOCUMENTATION.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\docs\ARCHITECTURE_DESIGN_DOCUMENTATION.md)
- Technical documentation: [docs/TECHNICAL_DOCUMENTATION.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\docs\TECHNICAL_DOCUMENTATION.md)
- Developer handoff / technical documentation: [docs/DEVELOPER_DOCUMENTATION.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\docs\DEVELOPER_DOCUMENTATION.md)
- Email setup notes: [EMAIL_SETUP.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\EMAIL_SETUP.md)
- Change history: [CHANGELOG.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\CHANGELOG.md)

## Tech Stack

- Laravel 12
- PHP 8.2+
- Blade templates
- MySQL / MariaDB or SQLite depending on environment
- Vite
- Tailwind CSS / custom Blade styling
- DOMPDF for PDF generation

## Quick Start

1. Install PHP and Composer dependencies.
2. Install Node dependencies.
3. Copy `.env.example` to `.env`.
4. Configure database and mail settings.
5. Run migrations.
6. Build frontend assets.
7. Start the Laravel development server.

Typical commands:

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```

## Project Structure

- `app/Http/Controllers` - main request handling logic
- `app/Models` - Eloquent models and relationships
- `app/Services` - activity log and notification services
- `database/migrations` - database schema history
- `database/seeders` - sample/bootstrap data seeders
- `resources/views` - Blade templates
- `routes/web.php` - application routes
- `scripts` - import/migration helper scripts for document batches
- `public/images` - logos and branding assets

## Core Modules

- Documents
- Financial Monitoring
- To-Do Monitoring
- User Management
- Office Management
- Notifications
- Table Reports / Print Preview
- Public Document Tracking

## Goal of This Documentation

This repository now includes developer-focused documentation so the system can be handed over and continued by another developer even if the current developer leaves the project.

If you are the next developer, start with:

1. [docs/PICTO_RTS_MASTER_DOCUMENTATION.md](C:\Users\Clarisahaina\xampp\htdocs\pictorts\docs\PICTO_RTS_MASTER_DOCUMENTATION.md)
2. [routes/web.php](C:\Users\Clarisahaina\xampp\htdocs\pictorts\routes\web.php)
3. [app/Models](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Models)
4. [app/Http/Controllers](C:\Users\Clarisahaina\xampp\htdocs\pictorts\app\Http\Controllers)
5. [resources/views](C:\Users\Clarisahaina\xampp\htdocs\pictorts\resources\views)
