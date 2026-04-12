# Email Functionality Setup Guide

This document explains how to set up and use the email functionality in PICTORTS.

## Configuration

### 1. Update .env File

Update your `.env` file with your email configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pictorts.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Gmail Setup (Recommended)

For Gmail, you need to:
1. Enable 2-factor authentication on your Gmail account
2. Generate an App Password:
   - Go to Google Account settings
   - Security -> 2-Step Verification -> App passwords
   - Generate a new app password for your application
3. Use the app password in the `MAIL_PASSWORD` field

### 3. Alternative Email Services

You can also use other email providers:

**SendGrid:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=YOUR_SENDGRID_API_KEY
MAIL_ENCRYPTION=tls
```

**Mailgun:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=YOUR_MAILGUN_USERNAME
MAIL_PASSWORD=YOUR_MAILGUN_PASSWORD
MAIL_ENCRYPTION=tls
```

## Features

### 1. Password Reset Emails

The system automatically sends password reset emails when users request a password reset. The email includes:
- Custom PICTORTS branding
- Reset link with token
- Security notice
- Expiration information

### 2. System Notifications

The system can send various types of notifications:

- **Document Notifications**: Status updates for documents
- **Financial Notifications**: Updates for financial records
- **Task Assignments**: New task assignments
- **System Alerts**: General system notifications
- **Welcome Emails**: Sent to new users

### 3. Email Service

The `EmailNotificationService` class provides methods for sending different types of emails:

```php
use App\Services\EmailNotificationService;

$emailService = app(EmailNotificationService::class);

// Send document notification
$emailService->sendDocumentNotification($user, 'Document Title', 'APPROVED');

// Send financial notification
$emailService->sendFinancialNotification($user, 'Invoice', '$1,000', 'PAID');

// Send task assignment
$emailService->sendTaskAssignmentNotification($user, 'Task Title', 'Description', '2024-01-01');

// Send system alert
$emailService->sendSystemAlert($user, 'Alert Type', 'Alert message');

// Send welcome email
$emailService->sendWelcomeEmail($user);

// Send to all admins
$emailService->sendToAdmins('Subject', 'Title', 'Message');

// Send to office users
$emailService->sendToOffice($officeId, 'Subject', 'Title', 'Message');
```

## Testing

### 1. Access Email Testing Page

Navigate to `/notifications` in your browser to access the email testing interface.

### 2. Test Different Email Types

The testing page allows you to:
- Send test notifications to yourself
- Send document status notifications
- Send financial record notifications
- Send task assignment notifications
- Send welcome emails

### 3. Check Email Logs

If emails are not being sent, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Mail configuration in `.env`
3. SMTP server connectivity
4. Email credentials

## Email Templates

### 1. Base Layout

The base email layout is in `resources/views/emails/layouts/app.blade.php` and includes:
- PICTORTS branding
- Responsive design
- Consistent styling
- Professional appearance

### 2. Password Reset Template

Located at `resources/views/emails/password-reset.blade.php`

### 3. Notification Template

Located at `resources/views/emails/notification.blade.php`

## Queue Configuration (Optional)

For better performance, you can queue emails:

### 1. Configure Queue

```env
QUEUE_CONNECTION=database
```

### 2. Run Queue Worker

```bash
php artisan queue:work
```

### 3. Install Supervisor (for production)

To ensure the queue worker stays running:

```bash
sudo apt-get install supervisor
```

Create a supervisor configuration file:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work
autostart=true
autorestart=true
user=www-data
num_processes=2
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
```

## Troubleshooting

### 1. Emails Not Sending

- Check `.env` configuration
- Verify SMTP credentials
- Check firewall settings
- Review Laravel logs

### 2. Authentication Issues

- Use app passwords for Gmail
- Verify username and password
- Check 2FA settings

### 3. Connection Issues

- Verify SMTP host and port
- Check encryption settings
- Test with telnet: `telnet smtp.gmail.com 587`

## Security Considerations

1. **Never commit email credentials to version control**
2. **Use environment variables for sensitive data**
3. **Use app passwords instead of regular passwords**
4. **Implement rate limiting for email sending**
5. **Monitor email sending logs for abuse**

## Customization

### 1. Modify Email Templates

Edit the Blade templates in `resources/views/emails/` to customize email appearance.

### 2. Add New Notification Types

Create new methods in `EmailNotificationService` for custom notification types.

### 3. Customize Email Styling

Modify the CSS in the email layout templates to match your brand.

## Production Deployment

1. Use a dedicated email service (SendGrid, Mailgun, etc.)
2. Configure proper DNS records (SPF, DKIM, DMARC)
3. Set up email monitoring and logging
4. Implement bounce handling
5. Set up unsubscribe functionality for marketing emails
