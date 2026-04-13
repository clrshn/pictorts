# Email Functionality Setup Guide

This document explains how to set up and use the email functionality in PICTORTS.

## Configuration

### 1. Update .env File

Update your `.env` file with your email configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=YOUR_SENDGRID_API_KEY
MAIL_ENCRYPTION=tls
MAIL_TIMEOUT=15
MAIL_FROM_ADDRESS="no-reply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Recommended Setup For Many Users

For a multi-user system, the recipient email is dynamic and comes from each user record.

Example:
- User A receives notifications at their own email
- User B receives notifications at their own email
- the app sends both through one verified sender account/domain

This means you do not configure one sender per user. You configure one proper mail service for the app, and every real user email can receive messages from it.

### 3. Alternative Email Services

Recommended providers for production:

**SendGrid:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=YOUR_SENDGRID_API_KEY
MAIL_ENCRYPTION=tls
MAIL_TIMEOUT=15
MAIL_FROM_ADDRESS="no-reply@yourdomain.com"
```

**Mailgun:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=YOUR_MAILGUN_USERNAME
MAIL_PASSWORD=YOUR_MAILGUN_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_TIMEOUT=15
MAIL_FROM_ADDRESS="no-reply@yourdomain.com"
```

### 4. Provider Notes

**SendGrid**
- Verify your sender identity or sending domain first
- Use `apikey` as `MAIL_USERNAME`
- Use the full SendGrid API key as `MAIL_PASSWORD`

**Mailgun**
- Use the SMTP credentials created in your Mailgun dashboard
- Verify your domain and DNS records before sending
- If your account is in the EU region, use the Mailgun SMTP host for that region

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

For local development, send emails immediately so password resets do not depend on a queue worker:

```env
QUEUE_CONNECTION=sync
```

For better performance in production, you can queue emails after SMTP is confirmed working:

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
- Verify your sender identity/domain in SendGrid or Mailgun
- Check firewall settings
- Review Laravel logs

### 2. Authentication Issues

- For SendGrid, `MAIL_USERNAME` must be `apikey`
- For SendGrid, `MAIL_PASSWORD` must be the full API key
- For Mailgun, use the provider SMTP credentials
- Verify username and password
- Verify the sender identity/domain is approved by the provider

### 3. Connection Issues

- Verify SMTP host and port
- Check encryption settings
- Test with telnet: `telnet smtp.sendgrid.net 587`

## Security Considerations

1. **Never commit email credentials to version control**
2. **Use environment variables for sensitive data**
3. **Use provider API keys or SMTP credentials instead of personal mailbox passwords**
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
