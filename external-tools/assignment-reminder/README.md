# Assignment Reminder Tool

## Overview

This External Tool checks for upcoming assignment deadlines and generates reminder notifications. It categorizes assignments by urgency and can output reminders in text or HTML format.

## Requirements

- PHP 7.4 or higher
- cURL extension enabled
- Access to the Core System API

## Usage

```bash
# Check assignments due within 7 days (default)
php assignment_reminder.php

# Check assignments due within 14 days
php assignment_reminder.php 14
```

## Output

The tool categorizes assignments into:

| Category | Description | Icon |
|----------|-------------|------|
| Overdue | Past due date | 🚨 |
| Due Today | Due today | ⚠️ |
| Due Tomorrow | Due tomorrow | 📅 |
| Due This Week | Due within threshold | 📆 |
| Upcoming | Due after threshold | 📋 |

## Output Files

- Console output: Text-based reminder with ASCII formatting
- HTML file: `reminder_YYYY-MM-DD.html` saved in the script directory

## Configuration

Edit the constants at the top of `assignment_reminder.php`:

```php
define('API_BASE_URL', 'http://localhost:8080/api');
define('INSTRUCTOR_EMAIL', 'instructor@example.com');
define('INSTRUCTOR_PASSWORD', 'password123');
```

## TODO for Team B

This is a **template** - you should enhance it with:

- [ ] Email notifications using PHPMailer
- [ ] SMS notifications using Twilio API
- [ ] Cron job integration for scheduled reminders
- [ ] Student-specific reminders (only their assignments)
- [ ] Configuration file support
- [ ] Database logging of sent reminders
- [ ] Customizable reminder templates
- [ ] Slack/Discord webhook integration

## Scheduling with Cron

To run daily reminders, add to crontab:

```bash
# Run every day at 8:00 AM
0 8 * * * /usr/bin/php /path/to/assignment_reminder.php 7
```

## API Endpoints Used

- `POST /api/auth/login` - Authentication
- `GET /api/assignments` - Fetch all assignments

See the [API Documentation](../../docs/API_DOCUMENTATION.md) for details.
